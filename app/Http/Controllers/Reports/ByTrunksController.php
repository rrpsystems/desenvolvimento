<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pbx;
use App\Models\Call;
use App\Models\Trunk;
use App\Models\Reports\ByTrunk;
use Codedge\Fpdf\Fpdf\Fpdf;

class ByTrunksController extends Controller
{
    public function __construct(Pbx $pbx, Call $call, Trunk $trunk, Bytrunk $bytrunk)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
        $this->trunk = $trunk;        
        $this->bytrunk = new $bytrunk('L', 'pt', 'A4');        
    }

    public function index()
    {
        $trunks = $this->trunk->select('trunk', 'tname','routes_route')->get()->groupBY('routes_route');
       
        return view('reports.bytrunks.index', compact('trunks'));
    }

   
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        
        $start_datetime = $request->input('start_date').' '.$request->input('start_time');
        $end_datetime   = $request->input('end_date').' '.$request->input('end_time');
        $trunks = $request->input('trunks');
        $directions = $request->input('directions');
        $dialNumber = $request->input('dialNumber');
        $types=$request->input('types');
        $types[]='INT';
        
        $request->merge([
            'start_date' => $start_datetime,
            'end_date'   => $end_datetime,
            'types'   => $types,
        ]);

        $request->validate([
            
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'trunks' => 'required|min:1',
            'dialNumber' => 'nullable|numeric',
            'directions' => 'required|min:1',
            'types' => 'required|min:1',
        ]);

        $calls = $this->call->select('calls.id AS cid', '*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->whereBetween('calldate', [$start_datetime, $end_datetime])
                                ->whereIn('trunks_id',$trunks)
                                ->whereIn('direction',$directions)
                                ->whereIn('cservice',$types)
                                ->where('dialnumber','like', '%'.$dialNumber.'%')
                                ->where('status_id','1')
                                ->orderBy('trunks_id','asc')
                                ->orderBy('calldate','asc')
                                ->get()
                                ->groupBy([
                                    'trunks_id',
                                        function ($item) {
                                            return date('d/m/Y', strtotime($item->calldate));
                                        }
                                    ], $preserveKeys = true);
        
        //altera a localização para ajustar as datas para portugues brasil
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
                                
        //Inicio Relatorio.
        $this->bytrunk->AliasNbPages();
                                
        //Insere um nome no cabeçalho
        $this->bytrunk->rName = 'Relatório Detalhado Por Troncos';
        //Insere a data de emissao
        $this->bytrunk->rTitle = utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today')));
        //VARIAVEIS DE SOMARIZAÇÃO
        $SIN = 0; $STIN = 0; $SVIN = 0;
        $SOC = 0; $STOC = 0; $SVOC = 0;
        $SIC = 0; $STIC = 0; $SVIC = 0;
        $SLIN= 0;

        if($calls->count() !=0):
            foreach($calls as $t => $ds):
                $trk = $this->trunk->where('trunk',$t)
                                    ->leftJoin('routes', 'routes_route', '=', 'route')
                                    ->first();
                
                $this->bytrunk->rTrunk = $trk->tname;
                $this->bytrunk->rRoute = $trk->routes_route;
                $this->bytrunk->rStart = $start_datetime;
                $this->bytrunk->rTrk   = $t;
                $this->bytrunk->rDdd   = $trk->ddd;
                $this->bytrunk->rEnd   = $end_datetime;
                                    
                // imprime cabeçalho da tabela
                $this->bytrunk->rPrint   = true;
                //dd($trk);
                $this->bytrunk->AddPage();
                // variaveis
                $lines = 0; $line = 0; 
                $IN = 0; $OC = 0; $IC = 0; 
                $TIN = 0; $TOC = 0; $TIC = 0; 
                $VIN = 0; $VOC = 0; $VIC = 0; 
                                        
                foreach($ds as $d => $cs):
                    //imprime a data
                    $line++;
                    $this->bytrunk->SetFillColor(224, 224, 224);
                    $this->bytrunk->Cell(60,19, utf8_decode($d), 0, 0, 'C', true);
                    $this->bytrunk->Cell(725,19, utf8_decode(''), 0, 1, 'C', true);
                    $true = false;
                                    
                    foreach($cs as $c):
                        $lines++;
                                    
                        if($c->direction == 'IN'):
                            $IN ++; $TIN += $c->billsec; $VIN += $c->rate;
                        
                        elseif($c->direction == 'OC'):
                            $OC ++; $TOC += $c->billsec; $VOC += $c->rate;
                        
                        elseif($c->direction == 'IC'):
                            $IC ++; $TIC += $c->billsec; $VIC += $c->rate;
                        
                        endif;
                        
                        //imprime as ligações
                        $this->bytrunk->Cell(25,19, utf8_decode(''), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(70,19, utf8_decode(date('H:i:s', strtotime($c->calldate))), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(80,19, utf8_decode(rdirection($c->direction)), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(70,19, utf8_decode($c->extensions_id), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(130,19, utf8_decode( substr($c->dialnumber,0,16)), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(180,19, utf8_decode( substr($c->locale,0,22) ), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(90,19, utf8_decode(rtype($c->cservice)), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(70,19, utf8_decode(gmdate("H:i:s", $c->billsec)), 0, 0, 'C', $true);
                        $this->bytrunk->Cell(70,19, utf8_decode(number_format($c->rate, 2, ',', '.')), 0, 1, 'C', $true);
                                            
                    endforeach;
                endforeach;
                                
                // verifica se a pagina contem mais de 16 linhas restantes para impressao do resumo dos ramais
                $footer = $lines+$line;
                if ( (($lines+$line) % 21) >= 16 || (($lines+$line) % 21) == 0):
                    $this->bytrunk->AddPage();
                endif;
                                
                // imprime o resuma das ligaçoes do ramal
                $this->bytrunk->SetY( 423 ); 
                $this->bytrunk->SetFillColor(224, 224, 224);
            
                $this->bytrunk->SetFont('arial', 'B', 11);
                $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->bytrunk->Cell(65, 19, utf8_decode("Resumo"), 0, 0, 'L', true);
                $this->bytrunk->Cell(130, 19, utf8_decode("Médio"), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode("Total"), 0, 0, 'C', true);
                $this->bytrunk->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
                $this->bytrunk->SetFont('arial', 'B', 11);
                $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->bytrunk->Cell(65, 19, utf8_decode("Tipo"), 0, 0, 'L');
                $this->bytrunk->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode("Valor"), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode("Qtd."), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
                $this->bytrunk->Cell(67, 19, utf8_decode("Valor"), 0, 1, 'C');
                $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytrunk->SetFont('arial', 'B', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode("Interno"), 0, 0, 'L', true);
                $this->bytrunk->setFont('arial', '', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode(secHours(($TIN / ($IN==0?1:$IN) ))), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format(($VIN / ($IN==0?1:$IN) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode($IN), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode(secHours($TIN)), 0, 0, 'C', true);
                $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format($VIN, 2, ',', '.')), 0, 1, 'C', true);
                $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytrunk->SetFont('arial', 'B', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode("Entrada"), 0, 0, 'L');
                $this->bytrunk->setFont('arial', '', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode(secHours(($TIC / ($IC==0?1:$IC) ))), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format(($VIC / ($IC==0?1:$IC) ), 2, ',', '.')), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode($IC), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode(secHours($TIC)), 0, 0, 'C');
                $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format($VIC, 2, ',', '.')), 0, 1, 'C');
                $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytrunk->SetFont('arial', 'B', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode("Saida"), 0, 0, 'L', true);
                $this->bytrunk->setFont('arial', '', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode(secHours(($TOC / ($OC==0?1:$OC) ))), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format(($VOC / ($OC==0?1:$OC) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode($OC), 0, 0, 'C', true);
                $this->bytrunk->Cell(65, 19, utf8_decode( secHours($TOC)), 0, 0, 'C', true);
                $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format($VOC, 2, ',', '.')), 0, 1, 'C', true);
                $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytrunk->SetFont('arial', 'B', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode("Total"), 0, 0, 'L');
                $this->bytrunk->setFont('arial', '', 11);
                $this->bytrunk->Cell(65, 19, utf8_decode(secHours(( ($TIN+$TIC+$TOC) / $lines)) ), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format( (($VIN+$VIC+$VOC) / $lines) , 2, ',', '.')), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode($lines), 0, 0, 'C');
                $this->bytrunk->Cell(65, 19, utf8_decode(secHours(($TIN+$TIC+$TOC))), 0, 0, 'C');
                $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format(($VIN+$VIC+$VOC), 2, ',', '.')), 0, 1, 'C');
                                    
                // variaveis para somarização total de todas as ligações
                $SIN  += $IN; $STIN += $TIN; $SVIN += $VIN;
                $SOC  += $OC; $STOC += $TOC; $SVOC += $VOC;
                $SIC  += $IC; $STIC += $TIC; $SVIC += $VIC;
                $SLIN += $lines;
            endforeach;
            
            // nao imprime o cabeçalho das tabelas
            $this->bytrunk->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->bytrunk->rTrunk  = 'TOTALIZAÇÃO DO RELATORIO';
            $this->bytrunk->rRoute = 'TOTALIZAÇÃO DO RELATORIO';
            $this->bytrunk->rStart = $start_datetime;
            $this->bytrunk->rTrk = 'TOTALIZAÇÃO DO RELATORIO';
            $this->bytrunk->rDdd = 'TOTALIZAÇÃO DO RELATORIO';
            $this->bytrunk->rEnd   = $end_datetime;
            
            $this->bytrunk->AddPage();
            $this->bytrunk->SetY( 423 );                      
            $this->bytrunk->SetFillColor(224, 224, 224);
            
            $this->bytrunk->SetFont('arial', 'B', 11);
            $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->bytrunk->Cell(65, 19, utf8_decode("Resumo"), 0, 0, 'L', true);
            $this->bytrunk->Cell(130, 19, utf8_decode("Médio"), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode("Total"), 0, 0, 'C', true);
            $this->bytrunk->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
            $this->bytrunk->SetFont('arial', 'B', 11);
            $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->bytrunk->Cell(65, 19, utf8_decode("Tipo"), 0, 0, 'L');
            $this->bytrunk->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode("Valor"), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode("Qtd."), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
            $this->bytrunk->Cell(67, 19, utf8_decode("Valor"), 0, 1, 'C');
            $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytrunk->SetFont('arial', 'B', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode("Interno"), 0, 0, 'L', true);
            $this->bytrunk->setFont('arial', '', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode( secHours( ($STIN / ($SIN==0?1:$SIN) ))), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIN / ($SIN==0?1:$SIN) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode($SIN), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode( secHours($STIN)), 0, 0, 'C', true);
            $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format($SVIN, 2, ',', '.')), 0, 1, 'C', true);
            $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytrunk->SetFont('arial', 'B', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode("Entrada"), 0, 0, 'L');
            $this->bytrunk->setFont('arial', '', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode( secHours( ($STIC / ($SIC==0?1:$SIC) ))), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIC / ($SIC==0?1:$SIC) ), 2, ',', '.')), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode($SIC), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode( secHours($STIC) ), 0, 0, 'C');
            $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format($SVIC, 2, ',', '.')), 0, 1, 'C');
            $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytrunk->SetFont('arial', 'B', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode("Saida"), 0, 0, 'L', true);
            $this->bytrunk->setFont('arial', '', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode( secHours( ($STOC / ($SOC==0?1:$SOC) ) ) ), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format(($SVOC / ($SOC==0?1:$SOC) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode($SOC), 0, 0, 'C', true);
            $this->bytrunk->Cell(65, 19, utf8_decode( secHours($STOC)), 0, 0, 'C', true);
            $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format($SVOC, 2, ',', '.')), 0, 1, 'C', true);
            $this->bytrunk->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytrunk->SetFont('arial', 'B', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode("Total"), 0, 0, 'L');
            $this->bytrunk->setFont('arial', '', 11);
            $this->bytrunk->Cell(65, 19, utf8_decode(secHours( ($STIN+$STIC+$STOC) / $SLIN) ), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode("R$ ". number_format( (($SVIN+$SVIC+$SVOC) / $SLIN) , 2, ',', '.')), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode($SLIN), 0, 0, 'C');
            $this->bytrunk->Cell(65, 19, utf8_decode(secHours($STIN+$STIC+$STOC)), 0, 0, 'C');
            $this->bytrunk->Cell(67, 19, utf8_decode("R$ ". number_format(($SVIN+$SVIC+$SVOC), 2, ',', '.')), 0, 1, 'C');

        else:
            //NÃO HA DADOS
            $this->bytrunk->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->bytrunk->rTrunk  = 'NÃO HA DADOS PARA O PERIODO';
            $this->bytrunk->rRoute = 'OU FILTROS SELECIONADO';
            $this->bytrunk->rStart = $start_datetime;
            $this->bytrunk->rTrk = 'NÃO HA DADOS PARA O PERIODO';
            $this->bytrunk->rDdd = 'OU FILTROS SELECIONADO';
            $this->bytrunk->rEnd   = $end_datetime;
            
            $this->bytrunk->AddPage();

        endif;

        $file = auth()->user()->name.' byTrunk.pdf';
        $this->bytrunk->Output($file,'F');
        $url = asset($file);
                          
        return view('reports.bytrunks.show', compact('url', 'file'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
