<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pbx;
use App\Models\Call;
use App\Models\Extension;
use App\Models\Departament;
use App\Models\Section;
use App\Models\Tenant;
use App\Models\Reports\ByExtension;
use Codedge\Fpdf\Fpdf\Fpdf;

class ByExtensionsController extends Controller
{

    public function __construct(Pbx $pbx, Call $call, Extension $extension, Departament $departament, Section $section, Tenant $tenant, ByExtension $byextension)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
        $this->extension = $extension;        
        $this->departament = $departament;        
        $this->section = $section;        
        $this->tenant = $tenant;        
        $this->byextension = new $byextension('L', 'pt', 'A4');        
    }

    public function index()
    {
        $authUser = auth()->user()->email;
        
        $extenUser = $this->extension->where('users_id',$authUser)->first();
        
        if(auth()->user()->hasRole('Master')):
            $extensions = $this->extension->get()->groupBy('pbxes_id');
            goto finish;
        endif;

        if(!$extenUser):
            $extensions = [];
            goto finish;
        endif;

        if(auth()->user()->can('rep_bygroups-list')):
            if($extenUser->groups_id):
                $extensions = $this->extension
                                        ->where('groups_id', $extenUser->groups_id )
                                        ->where('groups_id', '<>','')
                                        ->get()->groupBy('pbxes_id');
                goto finish;
            endif;
        endif;

        if(auth()->user()->can('rep_bytenants-list')):
            $sect = $this->tenant->join('sections', 'tenants_id', 'tenant')
                                ->join('departaments', 'section','=','sections_id')
                                ->where('departament','=',$extenUser->departaments_id)
                                ->where('departament','<>','')
                                ->first(); 
            if($sect):
                $extensions =  $this->tenant->join('sections', 'tenants_id', 'tenant')
                                            ->join('departaments', 'section','=','sections_id')
                                            ->join('extensions', 'departaments_id', 'departament')
                                            ->where('tenant', $sect->tenant)
                                            ->where('departaments_id','<>','')
                                            ->get()
                                            ->groupBy('departaments_id');
                goto finish;
            endif;
        endif;

        if(auth()->user()->can('rep_bysections-list')):
            $dpto = $this->section->join('departaments', 'section','=','sections_id')
                                  ->where('departament','=',$extenUser->departaments_id)
                                  ->where('departament','<>','')
                                  ->first(); 
            if($dpto):
                $extensions =  $this->section->join('departaments', 'section','=','sections_id')
                                        ->join('extensions', 'departaments_id', 'departament')
                                        ->where('section', $dpto->section)
                                        ->where('departaments_id','<>','')
                                        ->get()
                                        ->groupBy('departaments_id');
                goto finish;
            endif;
        endif;
                
        if(auth()->user()->can('rep_bydepartaments-list')):
            if($extenUser->departaments_id):
                $extensions = $this->extension
                                    ->where('departaments_id', $extenUser->departaments_id)
                                    ->where('departaments_id','<>','')
                                    ->get()->groupBy('pbxes_id');
                goto finish;
            endif;
        endif;
                
        if(auth()->user()->can('rep_byextensions-list')):
            $extensions = $this->extension->where('users_id', $authUser)->get()->groupBy('pbxes_id');
            goto finish;
        endif;
            
        $extensions = [];

        finish:
        
        return view('reports.byextensions.index', compact('extensions'));
    }

   
    public function store(Request $request)
    {
        //dd($request->input('report'));
        $start_datetime = $request->input('start_date').' '.$request->input('start_time');
        $end_datetime   = $request->input('end_date').' '.$request->input('end_time');
        $extensions = $request->input('extensions');
        $directions = $request->input('directions');
        $dialNumber = $request->input('dialNumber');
        $types=$request->input('types');
        $types[]='INT';
        $report=implode(",", $request->input('report'));

        //dd($report);
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

            $calls = $this->call->select('calls.id AS cid', (DB::raw(
                                    "(SELECT phonename FROM phonebooks WHERE callnumber LIKE CONCAT(phonenumber,'%') ORDER BY length(phonenumber) DESC LIMIT 1) AS phonename "
                                )),'*')
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

        //altera a localização para ajustar as datas para portugues brasil
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
                                
        //Inicio Relatorio.
        $this->byextension->AliasNbPages();
        if($report == 'detail'):
            //Insere um nome no cabeçalho
            $this->byextension->rName = trans('reports.d_exten');
        else:
            //Insere um nome no cabeçalho
            $this->byextension->rName = trans('reports.c_exten');
        endif;
        //Insere a data de emissao
        $this->byextension->rTitle = utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today')));
        //VARIAVEIS DE SOMARIZAÇÃO
        $SIN = 0; $STIN = 0; $SVIN = 0;
        $SOC = 0; $STOC = 0; $SVOC = 0;
        $SIC = 0; $STIC = 0; $SVIC = 0;
        $SLIN= 0;
        if($calls->count() !=0):                    
            
            foreach($calls as $e => $ds):
                $exten = $this->extension->where('extension',$e)->first();
                $this->byextension->rUser = $exten->ename;
                $this->byextension->rGroup = $exten->groups_id;
                $this->byextension->rStart = $start_datetime;
                $this->byextension->rExten = $e;
                $this->byextension->rDepto = $exten->departaments_id;
                $this->byextension->rEnd   = $end_datetime;
                                    
                if($report == 'detail'):
                    // imprime cabeçalho da tabela
                    $this->byextension->rPrint   = true;
                else:
                    // Não imprime cabeçalho da tabela
                    $this->byextension->rPrint   = false;
                endif;
                $this->byextension->AddPage();
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
                            $this->byextension->Cell(70,19,  utf8_decode( ($c->accountcodes_id?'* ':'  ').date('d/m/Y', strtotime($c->calldate) ) ), 0, 0, 'C', $true);
                            $this->byextension->Cell(70,19,  utf8_decode( date('H:i:s', strtotime($c->calldate) )), 0, 0, 'C', $true);
                            $this->byextension->Cell(60,19,  utf8_decode( trans('reports.'.$c->direction) ), 0, 0, 'C', $true);
                            $this->byextension->Cell(50,19,  utf8_decode( $c->trunks_id), 0, 0, 'C', $true);
                            $this->byextension->Cell(120,19, utf8_decode( substr($c->dialnumber,0,16)), 0, 0, 'C', $true);
                            $this->byextension->Cell(155,19, utf8_decode( substr($c->locale,0,22) ), 0, 0, 'C', $true);
                            $this->byextension->Cell(155,19, utf8_decode( substr($c->phonename,0,22) ), 0, 0, 'C', $true);
                            $this->byextension->Cell(50,19,  utf8_decode( gmdate("H:i:s", $c->billsec) ), 0, 0, 'C', $true);
                            $this->byextension->Cell(55,19,  utf8_decode( number_format( $c->rate, 2, ',', '.') ), 0, 1, 'C', $true);
                        endif;                
                    endforeach;
                endforeach;
                                
                // verifica se a pagina contem mais de 16 linhas restantes para impressao do resumo dos ramais
                $footer = $lines+$line;
                if ( (($lines+$line) % 21) >= 16 || (($lines+$line) % 21) == 0):
                    $this->byextension->AddPage();
                endif;
                                
                // imprime o resumo das ligaçoes do ramal
                $this->byextension->SetY( 423 ); 
                $this->byextension->SetFillColor(224, 224, 224);
            
                $this->byextension->SetFont('arial', 'B', 11);
                $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
                $this->byextension->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
                $this->byextension->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
                $this->byextension->SetFont('arial', 'B', 11);
                $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->byextension->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
                $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byextension->SetFont('arial', 'B', 11);
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
                $this->byextension->setFont('arial', '', 11);
                $this->byextension->Cell(65, 19, utf8_decode(secHours(($TIN / ($IN==0?1:$IN) ))), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($VIN / ($IN==0?1:$IN) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode($IN), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode(secHours($TIN)), 0, 0, 'C', true);
                $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($VIN, 2, ',', '.')), 0, 1, 'C', true);
                $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byextension->SetFont('arial', 'B', 11);
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
                $this->byextension->setFont('arial', '', 11);
                $this->byextension->Cell(65, 19, utf8_decode(secHours(($TIC / ($IC==0?1:$IC) ))), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($VIC / ($IC==0?1:$IC) ), 2, ',', '.')), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode($IC), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode(secHours($TIC)), 0, 0, 'C');
                $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($VIC, 2, ',', '.')), 0, 1, 'C');
                $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byextension->SetFont('arial', 'B', 11);
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
                $this->byextension->setFont('arial', '', 11);
                $this->byextension->Cell(65, 19, utf8_decode(secHours(($TOC / ($OC==0?1:$OC) ))), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($VOC / ($OC==0?1:$OC) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode($OC), 0, 0, 'C', true);
                $this->byextension->Cell(65, 19, utf8_decode( secHours($TOC)), 0, 0, 'C', true);
                $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($VOC, 2, ',', '.')), 0, 1, 'C', true);
                $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->byextension->SetFont('arial', 'B', 11);
                $this->byextension->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
                $this->byextension->setFont('arial', '', 11);
                $this->byextension->Cell(65, 19, utf8_decode(secHours(( ($TIN+$TIC+$TOC) / $lines)) ), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format( (($VIN+$VIC+$VOC) / $lines) , 2, ',', '.')), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode($lines), 0, 0, 'C');
                $this->byextension->Cell(65, 19, utf8_decode(secHours(($TIN+$TIC+$TOC))), 0, 0, 'C');
                $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format(($VIN+$VIC+$VOC), 2, ',', '.')), 0, 1, 'C');
                                    
                // variaveis para somarização total de todas as ligações
                $SIN  += $IN; $STIN += $TIN; $SVIN += $VIN;
                $SOC  += $OC; $STOC += $TOC; $SVOC += $VOC;
                $SIC  += $IC; $STIC += $TIC; $SVIC += $VIC;
                $SLIN += $lines;
            endforeach;
                                    
            // nao imprime o cabeçalho das tabelas
            $this->byextension->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->byextension->rUser  = trans('reports.finish');
            $this->byextension->rGroup = trans('reports.finish');
            $this->byextension->rStart = $start_datetime;
            $this->byextension->rExten = trans('reports.finish');
            $this->byextension->rDepto = trans('reports.finish');
            $this->byextension->rEnd   = $end_datetime;
            
            $this->byextension->AddPage();
            $this->byextension->SetY( 423 );                      
            $this->byextension->SetFillColor(224, 224, 224);
            
            $this->byextension->SetFont('arial', 'B', 11);
            $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
            $this->byextension->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
            $this->byextension->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
            $this->byextension->SetFont('arial', 'B', 11);
            $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->byextension->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
            $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byextension->SetFont('arial', 'B', 11);
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
            $this->byextension->setFont('arial', '', 11);
            $this->byextension->Cell(65, 19, utf8_decode( secHours( ($STIN / ($SIN==0?1:$SIN) ))), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIN / ($SIN==0?1:$SIN) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode($SIN), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode( secHours($STIN)), 0, 0, 'C', true);
            $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($SVIN, 2, ',', '.')), 0, 1, 'C', true);
            $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byextension->SetFont('arial', 'B', 11);
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
            $this->byextension->setFont('arial', '', 11);
            $this->byextension->Cell(65, 19, utf8_decode( secHours( ($STIC / ($SIC==0?1:$SIC) ))), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIC / ($SIC==0?1:$SIC) ), 2, ',', '.')), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode($SIC), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode( secHours($STIC) ), 0, 0, 'C');
            $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($SVIC, 2, ',', '.')), 0, 1, 'C');
            $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byextension->SetFont('arial', 'B', 11);
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
            $this->byextension->setFont('arial', '', 11);
            $this->byextension->Cell(65, 19, utf8_decode( secHours( ($STOC / ($SOC==0?1:$SOC) ) ) ), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format(($SVOC / ($SOC==0?1:$SOC) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode($SOC), 0, 0, 'C', true);
            $this->byextension->Cell(65, 19, utf8_decode( secHours($STOC)), 0, 0, 'C', true);
            $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format($SVOC, 2, ',', '.')), 0, 1, 'C', true);
            $this->byextension->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->byextension->SetFont('arial', 'B', 11);
            $this->byextension->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
            $this->byextension->setFont('arial', '', 11);
            $this->byextension->Cell(65, 19, utf8_decode(secHours( ($STIN+$STIC+$STOC) / $SLIN) ), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode("R$ ". number_format( (($SVIN+$SVIC+$SVOC) / $SLIN) , 2, ',', '.')), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode($SLIN), 0, 0, 'C');
            $this->byextension->Cell(65, 19, utf8_decode(secHours($STIN+$STIC+$STOC)), 0, 0, 'C');
            $this->byextension->Cell(67, 19, utf8_decode("R$ ". number_format(($SVIN+$SVIC+$SVOC), 2, ',', '.')), 0, 1, 'C');
                                
        else:
            //NÃO HA DADOS
            $this->byextension->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->byextension->rUser   = trans('reports.empty');
            $this->byextension->rGroup  = trans('reports.empty');
            $this->byextension->rStart  = $start_datetime;
            $this->byextension->rExten  = trans('reports.empty');
            $this->byextension->rDepto  = trans('reports.empty');
            $this->byextension->rEnd    = $end_datetime;
            
            $this->byextension->AddPage();

        endif;

        $file = str_replace(' ','_', auth()->user()->name.' '.trans('reports.d_exten').'.pdf');
        $this->byextension->Output($file,'F');
        $url = asset($file);
                          
        return view('reports.byextensions.show', compact('url', 'file'));
    }

}
