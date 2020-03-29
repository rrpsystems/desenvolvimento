<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pbx;
use App\Models\Phonebook;
use App\Models\Reports\ByPhonebook;
use App\Models\Call;
use Codedge\Fpdf\Fpdf\Fpdf;

class ByPhonebooksController extends Controller
{
    
    public function __construct(Pbx $pbx, Call $call, Phonebook $phonebook, ByPhonebook $byphonebook)
    {
        $this->pbx = $pbx;        
        $this->call = $call;                
        $this->phonebook = $phonebook;        
        $this->byphonebook = new $byphonebook('L', 'pt', 'A4');        
    }

    public function index()
    {
        
        if(auth()->user()->hasRole('Master')):
            $phonebooks = $this->phonebook->orderBy('phonename')->get()
                                            ->groupBy(function ($item) {
                                                return strtoupper(substr($item->phonename,0,1));
                                            });
            goto finish;
        endif;

        if(auth()->user()->can('rep_byphonebooks-list')):
            $phonebooks = $this->phonebook->orderBy('phonename')->get()
                                            ->groupBy(function ($item) {
                                                return strtoupper(substr($item->phonename,0,1));
                                            });

                                            
            goto finish;
        endif;
            
        $phonebooks = [];    
        
        finish:
        
        return view('reports.byphonebooks.index', compact('phonebooks'));
    }

    public function store(Request $request)
    {
        
        $start_datetime = $request->input('start_date').' '.$request->input('start_time');
        $end_datetime   = $request->input('end_date').' '.$request->input('end_time');
        $phonebooks     = $request->input('phonebooks');
        $directions     = $request->input('directions');
        $dialNumber     = $request->input('dialNumber');
        $types          = $request->input('types');
        $types[]        = 'INT';
        $report         = implode(",", $request->input('report'));

        $request->merge([
            'start_date' => $start_datetime,
            'end_date'   => $end_datetime,
            'types'      => $types,
        ]);

        $request->validate([
            
            'start_date' => 'required|date|before:end_date',
            'end_date'   => 'required|date|after:start_date',
            'phonebooks' => 'required|min:1',
            'dialNumber' => 'nullable|numeric',
            'directions' => 'required|min:1',
            'types'      => 'required|min:1',
        ]);

            $calls = $this->call->select('calls.id AS cid', (DB::raw(
                                    "(SELECT phonename FROM phonebooks WHERE callnumber LIKE CONCAT(phonenumber,'%') ORDER BY length(phonenumber) DESC LIMIT 1) AS phonename "
                                )),'*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->leftJoin('extensions', 'extensions_id', '=', 'extension')
                                ->leftJoin('accountcodes', 'accountcodes_id', '=', 'accountcode')
                                ->whereBetween('calldate', [$start_datetime, $end_datetime])
                                ->whereIn('direction',$directions)
                                ->whereIn('cservice',$types)
                                ->where('dialnumber','like', '%'.$dialNumber.'%')
                                ->where('status_id','1')
                                ->orderBy('calldate','asc')
                                ->get()
                                ->whereIn('phonename', $phonebooks)
                                ->groupBy([
                                           function ($item) {
                                                return $item->phonename;
                                            },
                                            function ($item) {
                                                return date('d/m/Y', strtotime($item->calldate));
                                            }
                                        ], $preserveKeys = true);

            //dd($calls);
        //altera a localização para ajustar as datas para portugues brasil
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
                                
        //Inicio Relatorio.
        $this->byphonebook->AliasNbPages();
        if($report == 'detail'):
            //Insere um nome no cabeçalho
            $this->byphonebook->rName = trans('reports.d_pbook');
        else:
            //Insere um nome no cabeçalho
            $this->byphonebook->rName = trans('reports.c_pbook');
        endif;
        //Insere a data de emissao
        $this->byphonebook->rTitle = utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today')));
        //VARIAVEIS DE SOMARIZAÇÃO
        $SIN = 0; $STIN = 0; $SVIN = 0;
        $SOC = 0; $STOC = 0; $SVOC = 0;
        $SIC = 0; $STIC = 0; $SVIC = 0;
        $SLIN= 0;
        if($calls->count() !=0):                    
            
            foreach($calls as $p => $ds):
                $pbook = $this->phonebook->where('phonename',$p)->first();
                $this->byphonebook->rUser = $pbook->phonename;
                $this->byphonebook->rGroup = $pbook->id;
                $this->byphonebook->rStart = $start_datetime;
                $this->byphonebook->rExten = $pbook->phonenumber;
                $this->byphonebook->rDepto = $pbook->id;
                $this->byphonebook->rEnd   = $end_datetime;
                                    
                if($report == 'detail'):
                    // imprime cabeçalho da tabela
                    $this->byphonebook->rPrint   = true;
                else:
                    // Não imprime cabeçalho da tabela
                    $this->byphonebook->rPrint   = false;
                endif;
                $this->byphonebook->AddPage();
                // variaveis
                $lines = 0; $line = 0; 
                $IN = 0; $OC = 0; $IC = 0; 
                $TIN = 0; $TOC = 0; $TIC = 0; 
                $VIN = 0; $VOC = 0; $VIC = 0; 
                
                foreach($ds as $d => $cs):
                    //imprime a data
                
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

                        if($report == 'detail'):
                            //imprime as ligações
                            
                            $this->byphonebook->Cell(60,19,  utf8_decode( ($c->accountcodes_id ? '* ' : '  ').date('d/m/Y', strtotime($c->calldate) ) ), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(60,19,  utf8_decode( date('H:i:s', strtotime($c->calldate) )), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(50,19,  utf8_decode( $c->extensions_id), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(145,19, utf8_decode( substr(($c->accountcodes_id ? $c->aname : $c->ename),0,22) ), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(55,19,  utf8_decode( trans('reports.'.$c->direction) ), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(50,19,  utf8_decode( $c->trunks_id), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(120,19, utf8_decode( substr($c->dialnumber,0,16)), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(150,19, utf8_decode( substr($c->locale,0,22) ), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(45,19,  utf8_decode( gmdate("H:i:s", $c->billsec) ), 0, 0, 'C', $true);
                            $this->byphonebook->Cell(50,19,  utf8_decode( number_format( $c->rate, 2, ',', '.') ), 0, 1, 'C', $true);
                        endif;                
                    endforeach;
                endforeach;
                                
                // verifica se a pagina contem mais de 16 linhas restantes para impressao do resumo dos ramais
                $footer = $lines+$line;
                if ( (($lines+$line) % 21) >= 16 || (($lines+$line) % 21) == 0):
                    $this->byphonebook->AddPage();
                endif;
                                
                // imprime o resumo das ligaçoes do ramal
                $this->byphonebook->SetY( 423 ); 
                $this->byphonebook->SetFillColor(224, 224, 224);
            
                $this->byphonebook->SetFont('arial', 'B', 11);
                $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
                $this->byphonebook->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
                $this->byphonebook->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
                $this->byphonebook->SetFont('arial', 'B', 11);
                $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->byphonebook->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
                $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byphonebook->SetFont('arial', 'B', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
                $this->byphonebook->setFont('arial', '', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(secHours(($TIN / ($IN==0?1:$IN) ))), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format(($VIN / ($IN==0?1:$IN) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode($IN), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode(secHours($TIN)), 0, 0, 'C', true);
                $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format($VIN, 2, ',', '.')), 0, 1, 'C', true);
                $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byphonebook->SetFont('arial', 'B', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
                $this->byphonebook->setFont('arial', '', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(secHours(($TIC / ($IC==0?1:$IC) ))), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format(($VIC / ($IC==0?1:$IC) ), 2, ',', '.')), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode($IC), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode(secHours($TIC)), 0, 0, 'C');
                $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format($VIC, 2, ',', '.')), 0, 1, 'C');
                $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byphonebook->SetFont('arial', 'B', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
                $this->byphonebook->setFont('arial', '', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(secHours(($TOC / ($OC==0?1:$OC) ))), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format(($VOC / ($OC==0?1:$OC) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode($OC), 0, 0, 'C', true);
                $this->byphonebook->Cell(65, 19, utf8_decode( secHours($TOC)), 0, 0, 'C', true);
                $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format($VOC, 2, ',', '.')), 0, 1, 'C', true);
                $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byphonebook->SetFont('arial', 'B', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
                $this->byphonebook->setFont('arial', '', 11);
                $this->byphonebook->Cell(65, 19, utf8_decode(secHours(( ($TIN+$TIC+$TOC) / $lines)) ), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format( (($VIN+$VIC+$VOC) / $lines) , 2, ',', '.')), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode($lines), 0, 0, 'C');
                $this->byphonebook->Cell(65, 19, utf8_decode(secHours(($TIN+$TIC+$TOC))), 0, 0, 'C');
                $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format(($VIN+$VIC+$VOC), 2, ',', '.')), 0, 1, 'C');
                                    
                // variaveis para somarização total de todas as ligações
                $SIN  += $IN; $STIN += $TIN; $SVIN += $VIN;
                $SOC  += $OC; $STOC += $TOC; $SVOC += $VOC;
                $SIC  += $IC; $STIC += $TIC; $SVIC += $VIC;
                $SLIN += $lines;
            endforeach;
                                    
            // nao imprime o cabeçalho das tabelas
            $this->byphonebook->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->byphonebook->rUser  = trans('reports.finish');
            $this->byphonebook->rGroup = trans('reports.finish');
            $this->byphonebook->rStart = $start_datetime;
            $this->byphonebook->rExten = trans('reports.finish');
            $this->byphonebook->rDepto = trans('reports.finish');
            $this->byphonebook->rEnd   = $end_datetime;
            
            $this->byphonebook->AddPage();
            $this->byphonebook->SetY( 423 );                      
            $this->byphonebook->SetFillColor(224, 224, 224);
            
            $this->byphonebook->SetFont('arial', 'B', 11);
            $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
            $this->byphonebook->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
            $this->byphonebook->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
            $this->byphonebook->SetFont('arial', 'B', 11);
            $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->byphonebook->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
            $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byphonebook->SetFont('arial', 'B', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
            $this->byphonebook->setFont('arial', '', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode( secHours( ($STIN / ($SIN==0?1:$SIN) ))), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIN / ($SIN==0?1:$SIN) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode($SIN), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode( secHours($STIN)), 0, 0, 'C', true);
            $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format($SVIN, 2, ',', '.')), 0, 1, 'C', true);
            $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byphonebook->SetFont('arial', 'B', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
            $this->byphonebook->setFont('arial', '', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode( secHours( ($STIC / ($SIC==0?1:$SIC) ))), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIC / ($SIC==0?1:$SIC) ), 2, ',', '.')), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode($SIC), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode( secHours($STIC) ), 0, 0, 'C');
            $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format($SVIC, 2, ',', '.')), 0, 1, 'C');
            $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byphonebook->SetFont('arial', 'B', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
            $this->byphonebook->setFont('arial', '', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode( secHours( ($STOC / ($SOC==0?1:$SOC) ) ) ), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format(($SVOC / ($SOC==0?1:$SOC) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode($SOC), 0, 0, 'C', true);
            $this->byphonebook->Cell(65, 19, utf8_decode( secHours($STOC)), 0, 0, 'C', true);
            $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format($SVOC, 2, ',', '.')), 0, 1, 'C', true);
            $this->byphonebook->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byphonebook->SetFont('arial', 'B', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
            $this->byphonebook->setFont('arial', '', 11);
            $this->byphonebook->Cell(65, 19, utf8_decode(secHours( ($STIN+$STIC+$STOC) / $SLIN) ), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode("R$ ". number_format( (($SVIN+$SVIC+$SVOC) / $SLIN) , 2, ',', '.')), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode($SLIN), 0, 0, 'C');
            $this->byphonebook->Cell(65, 19, utf8_decode(secHours($STIN+$STIC+$STOC)), 0, 0, 'C');
            $this->byphonebook->Cell(67, 19, utf8_decode("R$ ". number_format(($SVIN+$SVIC+$SVOC), 2, ',', '.')), 0, 1, 'C');
                                
        else:
            //NÃO HA DADOS
            $this->byphonebook->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->byphonebook->rUser   = trans('reports.empty');
            $this->byphonebook->rGroup  = trans('reports.empty');
            $this->byphonebook->rStart  = $start_datetime;
            $this->byphonebook->rExten  = trans('reports.empty');
            $this->byphonebook->rDepto  = trans('reports.empty');
            $this->byphonebook->rEnd    = $end_datetime;
            
            $this->byphonebook->AddPage();

        endif;

        $file = str_replace(' ','_', auth()->user()->name.' '.trans('reports.d_exten').'.pdf');
        $this->byphonebook->Output($file,'F');
        $url = asset($file);
                          
        return view('reports.byphonebooks.show', compact('url', 'file'));
    }

    
}
