<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pbx;
use App\Models\Call;
use App\Models\Extension;
use App\Models\Reports\ByExtension;
use Codedge\Fpdf\Fpdf\Fpdf;

class ByExtensionsController extends Controller
{

    public function __construct(Pbx $pbx, Call $call, Extension $extension, ByExtension $byextension)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
        $this->extension = $extension;        
        $this->byextension = new $byextension('L', 'pt', 'A4');        
    }

    public function index()
    {
        $extensions = $this->extension->select('extension', 'ename','pbxes_id')->get()->groupBY('pbxes_id');
        
        return view('reports.byextensions.index', compact('extensions'));
    }

   
    public function create()
    {
        //
    }

   
    //public function store(Request $request, Fpdf $fpdf)
    public function store(Request $request)
    {

        $start_datetime = $request->input('start_date').' '.$request->input('start_time');
        $end_datetime   = $request->input('end_date').' '.$request->input('end_time');
        $extensions = $request->input('extensions');
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
            'extensions' => 'required|min:1',
            'dialNumber' => 'nullable|numeric',
            'directions' => 'required|min:1',
            'types' => 'required|min:1',
        ]);

        //$extens = $this->extension->select('extension','ename')->get()->groupBy('extension');
        $calls = $this->call->select('calls.id AS cid', '*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->whereBetween('calldate', [$start_datetime, $end_datetime])
                                ->whereIn('extensions_id',$extensions)
                                ->whereIn('direction',$directions)
                                ->whereIn('cservice',$types)
                                ->where('dialnumber','like', '%'.$dialNumber.'%')
                                ->where('status_id','1')
                                ->orderBy('extensions_id','asc')
                                ->orderBy('calldate','asc')
                                ->get()
                                ->groupBy([
                                    'extensions_id',
                                    function ($item) {
                                        return date('d/m/Y', strtotime($item->calldate));
                                    }

                                ], $preserveKeys = true);

                                setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                                date_default_timezone_set('America/Sao_Paulo');
                                
                                //Inicio Relatorio.
                                $this->byextension->AliasNbPages();
                                //Insere um nome no cabeçalho
                                $this->byextension->rName = 'Relatório por Detalhado Por Ramais';
                                //Insere a data de emissao
                                $this->byextension->rTitle = utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today')));
                                //VARIAVEIS DE SOMARIZAÇÃO
                                $SIN = 0; $STIN = 0; $SVIN = 0;
                                $SOC = 0; $STOC = 0; $SVOC = 0;
                                $SIC = 0; $STIC = 0; $SVIC = 0;
                                $SLIN= 0;

                                foreach($calls as $e => $ds):

                                    //$rTitle, $rName, $rUser, $rGroup, $rStart, $rExten, $rDepto, $rEnd;
                                    //cabeçalho do relatorio 
                                    $exten = $this->extension->where('extension',$e)->first();
                                    $this->byextension->rUser = $exten->ename;
                                    $this->byextension->rGroup = $exten->groups_id;
                                    $this->byextension->rStart = $start_datetime;
                                    $this->byextension->rExten = $e;
                                    $this->byextension->rDepto = $exten->departaments_id;
                                    $this->byextension->rEnd   = $end_datetime;
                                    // imprime cabeçalho da tabela
                                    $this->byextension->rPrint   = true;
                                    
                                    $this->byextension->AddPage();
                                    // variaveis
                                    $lines = 0; 
                                    $line = 0; 
                                    $IN = 0; $OC = 0; $IC = 0; 
                                    $TIN = 0; $TOC = 0; $TIC = 0; 
                                    $VIN = 0; $VOC = 0; $VIC = 0; 
                                    
                                    foreach($ds as $d => $cs):
                                        //imprime a data
                                        $line++;
                                        $this->byextension->SetFillColor(224, 224, 224);
                                        $this->byextension->Cell(60,19, utf8_decode($d), 0, 0, 'C', true);
                                        $this->byextension->Cell(725,19, utf8_decode(''), 0, 1, 'C', true);
                                            
                                        $true = false;
                                
                                        foreach($cs as $c):
                                            //$footer++;
                                            $lines++;
                                            if($c->direction == 'IN'):
                                                $IN ++; 
                                                $TIN += $c->billsec; 
                                                $VIN += $c->rate;
                                            elseif($c->direction == 'OC'):
                                                $OC ++;
                                                $TOC += $c->billsec; 
                                                $VOC += $c->rate;
                                            elseif($c->direction == 'IC'):
                                                $IC ++;
                                                $TIC += $c->billsec; 
                                                $VIC += $c->rate;
                                            endif;
                                            //$this->byextension->Cell(50,19, utf8_decode( $lines ), 0, 0, 'C', $true);
                                            //$this->byextension->Cell(55,19, utf8_decode(date('d/m/Y', strtotime($c->calldate))), 0, 0, 'C', $true);
                                            $this->byextension->Cell(25,19, utf8_decode(''), 0, 0, 'C', $true);
                                            $this->byextension->Cell(50,19, utf8_decode(date('H:i:s', strtotime($c->calldate))), 0, 0, 'C', $true);
                                            $this->byextension->Cell(60,19, utf8_decode(rdirection($c->direction)), 0, 0, 'C', $true);
                                            $this->byextension->Cell(50,19, utf8_decode($c->trunks_id), 0, 0, 'C', $true);
                                            $this->byextension->Cell(110,19, utf8_decode( substr($c->dialnumber,0,16)), 0, 0, 'C', $true);
                                            $this->byextension->Cell(160,19, utf8_decode( substr($c->locale,0,22) ), 0, 0, 'C', $true);
                                            $this->byextension->Cell(160,19, utf8_decode(  substr('Agenda',0,22) ), 0, 0, 'C', $true);
                                            $this->byextension->Cell(70,19, utf8_decode(rtype($c->cservice)), 0, 0, 'C', $true);
                                            $this->byextension->Cell(50,19, utf8_decode(gmdate("H:i:s", $c->billsec)), 0, 0, 'C', $true);
                                            $this->byextension->Cell(50,19, utf8_decode(number_format($c->rate, 2, ',', '.')), 0, 1, 'C', $true);
                                            
                                            //$true = false;
                                        endforeach;

                                    endforeach;
                                $footer = $lines+$line;
                                if ( (($lines+$line) % 21) >= 16 || (($lines+$line) % 21) == 0):
                                    //dd(($lines+$line) % 21);
                                        //  $this->byextension->rPrint   = false;
                                        $this->byextension->AddPage();
                                    endif;
                                
                                   $this->byextension->SetY( 423 ); 
                                   
                                    $this->byextension->SetFillColor(224, 224, 224);
                                    $this->byextension->SetFont('arial', 'B', 11);
                                    $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                    $this->byextension->Cell(65, 19, utf8_decode("Resumo"), 0, 0, 'L', true);
                                    $this->byextension->Cell(130, 19, utf8_decode("Médio"), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode("Total"), 0, 0, 'C', true);
                                    $this->byextension->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);

                                    $this->byextension->SetFont('arial', 'B', 11);
                                    $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                    $this->byextension->Cell(65, 19, utf8_decode("Tipo"), 0, 0, 'L');
                                    $this->byextension->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode("Valor"), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode("Qtd."), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
                                    $this->byextension->Cell(67, 19, utf8_decode("Valor"), 0, 1, 'C');

                                    $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                    $this->byextension->SetFont('arial', 'B', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode("Interno"), 0, 0, 'L', true);
                                    $this->byextension->setFont('arial', '', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode(secHours(($TIN / ($IN==0?1:$IN) ))), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($VIN / ($IN==0?1:$IN) ), 2, ',', '.')), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode($IN), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode(secHours($TIN)), 0, 0, 'C', true);
                                    $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($VIN, 2, ',', '.')), 0, 1, 'C', true);

                                    $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                    $this->byextension->SetFont('arial', 'B', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode("Entrada"), 0, 0, 'L');
                                    $this->byextension->setFont('arial', '', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode(secHours(($TIC / ($IC==0?1:$IC) ))), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($VIC / ($IC==0?1:$IC) ), 2, ',', '.')), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode($IC), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode(secHours($TIC)), 0, 0, 'C');
                                    $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($VIC, 2, ',', '.')), 0, 1, 'C');

                                    $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                    $this->byextension->SetFont('arial', 'B', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode("Saida"), 0, 0, 'L', true);
                                    $this->byextension->setFont('arial', '', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode(secHours(($TOC / ($OC==0?1:$OC) ))), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($VOC / ($OC==0?1:$OC) ), 2, ',', '.')), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode($OC), 0, 0, 'C', true);
                                    $this->byextension->Cell(65, 19, utf8_decode( secHours($TOC)), 0, 0, 'C', true);
                                    $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($VOC, 2, ',', '.')), 0, 1, 'C', true);

                                    $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                    $this->byextension->SetFont('arial', 'B', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode("Total"), 0, 0, 'L');
                                    $this->byextension->setFont('arial', '', 11);
                                    $this->byextension->Cell(65, 19, utf8_decode(secHours(( ($TIN+$TIC+$TOC) / $lines)) ), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format( (($VIN+$VIC+$VOC) / $lines) , 2, ',', '.')), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode($lines), 0, 0, 'C');
                                    $this->byextension->Cell(65, 19, utf8_decode(secHours(($TIN+$TIC+$TOC))), 0, 0, 'C');
                                    $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format(($VIN+$VIC+$VOC), 2, ',', '.')), 0, 1, 'C');
                                    
                                    $SIN  += $IN; $STIN += $TIN; $SVIN += $VIN;
                                    $SOC  += $OC; $STOC += $TOC; $SVOC += $VOC;
                                    $SIC  += $IC; $STIC += $TIC; $SVIC += $VIC;
                                    $SLIN += $lines;
                                endforeach;


                                      $this->byextension->rPrint = false;
                                      $this->byextension->rUser  = 'TOTALIZAÇÃO DO RELATORIO';
                                      $this->byextension->rGroup = 'TOTALIZAÇÃO DO RELATORIO';
                                      $this->byextension->rStart = $start_datetime;
                                      $this->byextension->rExten = 'TOTALIZAÇÃO DO RELATORIO';
                                      $this->byextension->rDepto = 'TOTALIZAÇÃO DO RELATORIO';
                                      $this->byextension->rEnd   = $end_datetime;
                                      $this->byextension->AddPage();
                                
                                 $this->byextension->SetY( 423 ); 
                                 
                                  $this->byextension->SetFillColor(224, 224, 224);
                                  $this->byextension->SetFont('arial', 'B', 11);
                                  $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                  $this->byextension->Cell(65, 19, utf8_decode("Resumo"), 0, 0, 'L', true);
                                  $this->byextension->Cell(130, 19, utf8_decode("Médio"), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode("Total"), 0, 0, 'C', true);
                                  $this->byextension->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);

                                  $this->byextension->SetFont('arial', 'B', 11);
                                  $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                  $this->byextension->Cell(65, 19, utf8_decode("Tipo"), 0, 0, 'L');
                                  $this->byextension->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode("Valor"), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode("Qtd."), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode("Duração"), 0, 0, 'C');
                                  $this->byextension->Cell(67, 19, utf8_decode("Valor"), 0, 1, 'C');

                                  $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                  $this->byextension->SetFont('arial', 'B', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode("Interno"), 0, 0, 'L', true);
                                  $this->byextension->setFont('arial', '', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode( secHours( ($STIN / ($SIN==0?1:$SIN) ))), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIN / ($SIN==0?1:$SIN) ), 2, ',', '.')), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode($SIN), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode( secHours($STIN)), 0, 0, 'C', true);
                                  $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($SVIN, 2, ',', '.')), 0, 1, 'C', true);

                                  $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                  $this->byextension->SetFont('arial', 'B', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode("Entrada"), 0, 0, 'L');
                                  $this->byextension->setFont('arial', '', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode( secHours( ($STIC / ($SIC==0?1:$SIC) ))), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIC / ($SIC==0?1:$SIC) ), 2, ',', '.')), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode($SIC), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode( secHours($STIC) ), 0, 0, 'C');
                                  $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($SVIC, 2, ',', '.')), 0, 1, 'C');

                                  $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                  $this->byextension->SetFont('arial', 'B', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode("Saida"), 0, 0, 'L', true);
                                  $this->byextension->setFont('arial', '', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode( secHours( ($STOC / ($SOC==0?1:$SOC) ) ) ), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($SVOC / ($SOC==0?1:$SOC) ), 2, ',', '.')), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode($SOC), 0, 0, 'C', true);
                                  $this->byextension->Cell(65, 19, utf8_decode( secHours($STOC)), 0, 0, 'C', true);
                                  $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($SVOC, 2, ',', '.')), 0, 1, 'C', true);

                                  $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                                  $this->byextension->SetFont('arial', 'B', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode("Total"), 0, 0, 'L');
                                  $this->byextension->setFont('arial', '', 11);
                                  $this->byextension->Cell(65, 19, utf8_decode(secHours( ($STIN+$STIC+$STOC) / $SLIN) ), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format( (($SVIN+$SVIC+$SVOC) / $SLIN) , 2, ',', '.')), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode($SLIN), 0, 0, 'C');
                                  $this->byextension->Cell(65, 19, utf8_decode(secHours($STIN+$STIC+$STOC)), 0, 0, 'C');
                                  $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format(($SVIN+$SVIC+$SVOC), 2, ',', '.')), 0, 1, 'C');
                                 

                                  //$response = response($this->byextension->Output('S'));
                                //dd($response);
                                  //$this->byextension->Output('S');
                                  //dd(public_path());
                                  //$url = Storage::url('file.jpg');
                                  $file = auth()->user()->name.'byExtension.pdf';
                                  $this->byextension->Output($file,'F');
                                  $url = asset($file);
                                  //dd($url);
                                  //dd($teste);
                                  //$response->header('Content-Type', 'application/pdf');
                                  //$response->header('Content-Disposition', 'inline; filename="output.pdf"');
                                  //$response->header('Cache-Control:', 'private, max-age=0, must-revalidate');
                      
                                  //return $response;
                                //$file = $this->byextension->Output('D');
                                
                                //$filename = 'test.pdf';
                                //$path = storage_path($file);
                                //dd($path);

//return Response::make(file_get_contents($path), 200, [
//    'Content-Type' => 'application/pdf',
//    'Content-Disposition' => 'inline; filename="'.$filename.'"'
//]);

                                //$fpdf->Output('filename.pdf','D');
        //$extens = json_decode(json_encode($extens));
        //dd($extens);
        
        return view('reports.byextensions.show', compact('url', 'file'));
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
