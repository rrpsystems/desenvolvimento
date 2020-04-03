<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pbx;
use App\Models\Tenant;
use App\Models\Section;
use App\Models\Departament;
use App\Models\Extension;
use App\Models\Accountcode;
use App\Models\Call;
use App\Models\Reports\ByTenant;
use Codedge\Fpdf\Fpdf\Fpdf;

class ByTenantsController extends Controller
{
    
    public function __construct(Pbx $pbx, Call $call, Departament $departament, Section $section, Tenant $tenant, ByTenant $bytenant, Extension $extension, Accountcode $accountcode)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
        $this->departament = $departament;        
        $this->section = $section;        
        $this->tenant = $tenant;        
        $this->extension = $extension;        
        $this->accountcode = $accountcode;        
        $this->bytenant = new $bytenant('L', 'pt', 'A4');        
    }

    public function index()
    {
        $authUser = auth()->user()->email;
        
        $extenUser = $this->extension->where('users_id',$authUser)->first();
        $acccdUser = $this->accountcode->where('users_id',$authUser)->first();
        
        if($acccdUser && $extenUser):
                $deptoUser = [$acccdUser->departaments_id,$extenUser->departaments_id];
            
        elseif($acccdUser):
            $deptoUser = [$acccdUser->departaments_id];

        elseif($extenUser):
            $deptoUser = [$extenUser->departaments_id];

        endif;
        
            if(auth()->user()->hasRole('Master')):
                $tenants = $this->tenant->get()->groupBy('pbxes_id');
                goto finish;
            endif;

            if(!$extenUser):
                $sections = [];
                goto finish;
            endif;

            if(auth()->user()->can('rep_bytenants-list')):
                if($deptoUser):
                    $tenants = $this->tenant->join('sections', 'tenants_id', 'tenant')
                                    ->join('departaments', 'section','=','sections_id')
                                    ->whereIn('departament',$deptoUser)
                                    ->where('departament','<>','')
                                    ->get()
                                    ->groupBy('pbxes_id');
                                    
                    goto finish;
                endif;
            endif;
               
            $tenants = [];    
            
            finish:
            
        return view('reports.bytenants.index', compact('tenants'));
    }

   
    public function store(Request $request)
    {
        
        $start_datetime = $request->input('start_date').' '.$request->input('start_time');
        $end_datetime   = $request->input('end_date').' '.$request->input('end_time');
        $tenants = $request->input('tenants');
        $directions = $request->input('directions');
        $dialNumber = $request->input('dialNumber');
        $types=$request->input('types');
        $types[]='INT';
        $report=implode(",", $request->input('report'));

        $request->merge([
            'start_date' => $start_datetime,
            'end_date'   => $end_datetime,
            'types'   => $types,
        ]);

        $request->validate([
            
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'tenants' => 'required|min:1',
            'dialNumber' => 'nullable|numeric',
            'directions' => 'required|min:1',
            'types' => 'required|min:1',
        ]);
        
            $ext = $this->tenant
                            ->leftJoin('sections', 'tenants_id','=','tenant')
                            ->leftJoin('departaments', 'sections_id','=','section')
                            ->leftJoin('extensions', 'departaments_id','=','departament')
                            ->whereIn('tenant',$tenants)
                            ->get();
            
            $acc = $this->tenant
                            ->leftJoin('sections', 'tenants_id','=','tenant')
                            ->leftJoin('departaments', 'sections_id','=','section')
                            ->leftJoin('accountcodes', 'departaments_id','=','departament')
                            ->whereIn('tenant',$tenants)
                            ->get();
            
            $exts = $ext->map(function ($item, $key) {
                                return $item->extension;
                            });
                            
            $accs = $acc->map(function ($item, $key) {
                                return $item->accountcode;
                            });
                            //dd($accs, $exts);        
            $calls = $this->call->select('calls.id AS cid', (DB::raw(
                                    "(SELECT phonename FROM phonebooks WHERE callnumber LIKE CONCAT(phonenumber,'%') ORDER BY length(phonenumber) DESC LIMIT 1) AS phonename "
                                )),'*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->leftJoin('extensions', 'extensions_id', '=', 'extension')
                                ->leftJoin('accountcodes', 'accountcodes_id', '=', 'accountcode')
                                ->leftJoin('departaments', 'extensions.departaments_id', '=', 'departament','OR','accountcodes.departaments_id', '=', 'departament')
                                ->leftJoin('sections', 'departaments.sections_id', '=', 'section')
                                ->leftJoin('tenants', 'sections.tenants_id', '=', 'tenant')
                                ->whereBetween('calldate', [$start_datetime, $end_datetime])
                                ->whereIn('direction',$directions)
                                ->whereIn('cservice',$types)
                                ->where('dialnumber','like', '%'.$dialNumber.'%')
                                ->where('status_id','1')
                                ->whereIn('extensions_id',$exts)
                                ->orWhereIn('accountcodes_id',$accs)
                                ->orderBy('departament','asc')
                                ->orderBy('calldate','asc')
                                ->get()
                                ->groupBy([
                                    'tenant',
                                        function ($item) {
                                            return date('d/m/Y', strtotime($item->calldate));
                                        }
                                    ], $preserveKeys = true);
                            
        //altera a localização para ajustar as datas para portugues brasil
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
             
        //Inicio Relatorio.
        $this->bytenant->AliasNbPages();
        if($report == 'detail'):
            //Insere um nome no cabeçalho
            $this->bytenant->rName = trans('reports.d_tenant');
        else:
            //Insere um nome no cabeçalho
            $this->bytenant->rName = trans('reports.c_tenant');
        endif;
        //Insere a data de emissao
        $this->bytenant->rTitle = utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today')));
        //VARIAVEIS DE SOMARIZAÇÃO
        $SIN = 0; $STIN = 0; $SVIN = 0;
        $SOC = 0; $STOC = 0; $SVOC = 0;
        $SIC = 0; $STIC = 0; $SVIC = 0;
        $SLIN= 0;
        if($calls->count() !=0):                    
            
            foreach($calls as $e => $ds):
                //$exten = $this->tenant->where('tenant',$e)->first();
                $this->bytenant->rUser = $e;
                $this->bytenant->rStart = $start_datetime;
                $this->bytenant->rExten = $e;
                $this->bytenant->rEnd   = $end_datetime;
                                    
                if($report == 'detail'):
                    // imprime cabeçalho da tabela
                    $this->bytenant->rPrint   = true;
                else:
                    // Não imprime cabeçalho da tabela
                    $this->bytenant->rPrint   = false;
                endif;
                $this->bytenant->AddPage();
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
                            $this->bytenant->Cell(60,19,  utf8_decode( ($c->accountcodes_id?'* ':'  ').date('d/m/Y', strtotime($c->calldate) ) ), 0, 0, 'C', $true);
                            $this->bytenant->Cell(60,19,  utf8_decode( date('H:i:s', strtotime($c->calldate) )), 0, 0, 'C', $true);
                            $this->bytenant->Cell(60,19,  utf8_decode( $c->extensions_id), 0, 0, 'C', $true);
                            $this->bytenant->Cell(120,19,  utf8_decode( $c->accountcodes_id ? $c->aname : $c->ename), 0, 0, 'C', $true);
                            $this->bytenant->Cell(65,19,  utf8_decode( trans('reports.'.$c->direction) ), 0, 0, 'C', $true);
                            $this->bytenant->Cell(60,19,  utf8_decode( $c->trunks_id), 0, 0, 'C', $true);
                            $this->bytenant->Cell(120,19, utf8_decode( substr($c->dialnumber,0,16)), 0, 0, 'C', $true);
                            $this->bytenant->Cell(150,19, utf8_decode( substr($c->locale,0,22) ), 0, 0, 'C', $true);
                            $this->bytenant->Cell(45,19,  utf8_decode( gmdate("H:i:s", $c->billsec) ), 0, 0, 'C', $true);
                            $this->bytenant->Cell(50,19,  utf8_decode( number_format( $c->rate, 2, ',', '.') ), 0, 1, 'C', $true);
                        endif;                
                    endforeach;
                endforeach;
                                
                if($report == 'detail'):
                    // verifica se a pagina contem mais de 16 linhas restantes para impressao do resumo dos ramais
                    $footer = $lines+$line;
                    if ( (($lines+$line) % 22) >= 16 || (($lines+$line) % 22) == 0):
                        $this->bytenant->AddPage();
                    endif;
                endif;            
                // imprime o resumo das ligaçoes do ramal
                $this->bytenant->SetY( 423 ); 
                $this->bytenant->SetFillColor(224, 224, 224);
            
                $this->bytenant->SetFont('arial', 'B', 11);
                $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
                $this->bytenant->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
                $this->bytenant->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
                $this->bytenant->SetFont('arial', 'B', 11);
                $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->bytenant->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
                $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytenant->SetFont('arial', 'B', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
                $this->bytenant->setFont('arial', '', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(secHours(($TIN / ($IN==0?1:$IN) ))), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format(($VIN / ($IN==0?1:$IN) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode($IN), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode(secHours($TIN)), 0, 0, 'C', true);
                $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format($VIN, 2, ',', '.')), 0, 1, 'C', true);
                $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytenant->SetFont('arial', 'B', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
                $this->bytenant->setFont('arial', '', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(secHours(($TIC / ($IC==0?1:$IC) ))), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format(($VIC / ($IC==0?1:$IC) ), 2, ',', '.')), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode($IC), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode(secHours($TIC)), 0, 0, 'C');
                $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format($VIC, 2, ',', '.')), 0, 1, 'C');
                $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytenant->SetFont('arial', 'B', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
                $this->bytenant->setFont('arial', '', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(secHours(($TOC / ($OC==0?1:$OC) ))), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format(($VOC / ($OC==0?1:$OC) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode($OC), 0, 0, 'C', true);
                $this->bytenant->Cell(65, 19, utf8_decode( secHours($TOC)), 0, 0, 'C', true);
                $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format($VOC, 2, ',', '.')), 0, 1, 'C', true);
                $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bytenant->SetFont('arial', 'B', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
                $this->bytenant->setFont('arial', '', 11);
                $this->bytenant->Cell(65, 19, utf8_decode(secHours(( ($TIN+$TIC+$TOC) / $lines)) ), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format( (($VIN+$VIC+$VOC) / $lines) , 2, ',', '.')), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode($lines), 0, 0, 'C');
                $this->bytenant->Cell(65, 19, utf8_decode(secHours(($TIN+$TIC+$TOC))), 0, 0, 'C');
                $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format(($VIN+$VIC+$VOC), 2, ',', '.')), 0, 1, 'C');
                                    
                // variaveis para somarização total de todas as ligações
                $SIN  += $IN; $STIN += $TIN; $SVIN += $VIN;
                $SOC  += $OC; $STOC += $TOC; $SVOC += $VOC;
                $SIC  += $IC; $STIC += $TIC; $SVIC += $VIC;
                $SLIN += $lines;
            endforeach;
                                    
            // nao imprime o cabeçalho das tabelas
            $this->bytenant->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->bytenant->rUser  = trans('reports.finish');
            $this->bytenant->rGroup = trans('reports.finish');
            $this->bytenant->rStart = $start_datetime;
            $this->bytenant->rExten = trans('reports.finish');
            $this->bytenant->rDepto = trans('reports.finish');
            $this->bytenant->rEnd   = $end_datetime;
            
            $this->bytenant->AddPage();
            $this->bytenant->SetY( 423 );                      
            $this->bytenant->SetFillColor(224, 224, 224);
            
            $this->bytenant->SetFont('arial', 'B', 11);
            $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
            $this->bytenant->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
            $this->bytenant->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
            $this->bytenant->SetFont('arial', 'B', 11);
            $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->bytenant->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
            $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytenant->SetFont('arial', 'B', 11);
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
            $this->bytenant->setFont('arial', '', 11);
            $this->bytenant->Cell(65, 19, utf8_decode( secHours( ($STIN / ($SIN==0?1:$SIN) ))), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIN / ($SIN==0?1:$SIN) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode($SIN), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode( secHours($STIN)), 0, 0, 'C', true);
            $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format($SVIN, 2, ',', '.')), 0, 1, 'C', true);
            $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytenant->SetFont('arial', 'B', 11);
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
            $this->bytenant->setFont('arial', '', 11);
            $this->bytenant->Cell(65, 19, utf8_decode( secHours( ($STIC / ($SIC==0?1:$SIC) ))), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIC / ($SIC==0?1:$SIC) ), 2, ',', '.')), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode($SIC), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode( secHours($STIC) ), 0, 0, 'C');
            $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format($SVIC, 2, ',', '.')), 0, 1, 'C');
            $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytenant->SetFont('arial', 'B', 11);
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
            $this->bytenant->setFont('arial', '', 11);
            $this->bytenant->Cell(65, 19, utf8_decode( secHours( ($STOC / ($SOC==0?1:$SOC) ) ) ), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format(($SVOC / ($SOC==0?1:$SOC) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode($SOC), 0, 0, 'C', true);
            $this->bytenant->Cell(65, 19, utf8_decode( secHours($STOC)), 0, 0, 'C', true);
            $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format($SVOC, 2, ',', '.')), 0, 1, 'C', true);
            $this->bytenant->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bytenant->SetFont('arial', 'B', 11);
            $this->bytenant->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
            $this->bytenant->setFont('arial', '', 11);
            $this->bytenant->Cell(65, 19, utf8_decode(secHours( ($STIN+$STIC+$STOC) / $SLIN) ), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode("R$ ". number_format( (($SVIN+$SVIC+$SVOC) / $SLIN) , 2, ',', '.')), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode($SLIN), 0, 0, 'C');
            $this->bytenant->Cell(65, 19, utf8_decode(secHours($STIN+$STIC+$STOC)), 0, 0, 'C');
            $this->bytenant->Cell(67, 19, utf8_decode("R$ ". number_format(($SVIN+$SVIC+$SVOC), 2, ',', '.')), 0, 1, 'C');
                                
        else:
            //NÃO HA DADOS
            $this->bytenant->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->bytenant->rUser   = trans('reports.empty');
            $this->bytenant->rGroup  = trans('reports.empty');
            $this->bytenant->rStart  = $start_datetime;
            $this->bytenant->rExten  = trans('reports.empty');
            $this->bytenant->rDepto  = trans('reports.empty');
            $this->bytenant->rEnd    = $end_datetime;
            
            $this->bytenant->AddPage();

        endif;

        $file = str_replace(' ','_', auth()->user()->name.' '.trans('reports.d_exten').'.pdf');
        $this->bytenant->Output($file,'F');
        $url = asset($file);
                          
        return view('reports.bytenants.show', compact('url', 'file'));
    }


}
