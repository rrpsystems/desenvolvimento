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
use App\Models\Reports\ByDepartament;
use Codedge\Fpdf\Fpdf\Fpdf;

class ByDepartamentsController extends Controller
{

    public function __construct(Pbx $pbx, Call $call, Departament $departament, Section $section, Tenant $tenant, ByDepartament $bydepartament, Extension $extension, Accountcode $accountcode)
    {
        $this->pbx = $pbx;        
        $this->call = $call;        
        $this->departament = $departament;        
        $this->section = $section;        
        $this->tenant = $tenant;        
        $this->extension = $extension;        
        $this->accountcode = $accountcode;        
        $this->bydepartament = new $bydepartament('L', 'pt', 'A4');        
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
                $departaments = $this->departament->get()->groupBy('pbxes_id');
                goto finish;
            endif;

            if(!$extenUser):
                $departaments = [];
                goto finish;
            endif;

            if(auth()->user()->can('rep_bytenants-list')):

                $sect = $this->tenant->join('sections', 'tenants_id', 'tenant')
                                    ->join('departaments', 'section','=','sections_id')
                                    ->whereIn('departament',$deptoUser)
                                    ->where('departament','<>','')
                                    ->first(); 
                                    
                if($sect):
                    $departaments =  $this->tenant->join('sections', 'tenants_id', 'tenant')
                                            ->join('departaments', 'section','=','sections_id')
                                            ->where('tenant', $sect->tenant)
                                            ->where('departament','<>','')
                                            ->get()
                                            ->groupBy('sections_id');
                                            
                    goto finish;
                endif;
            endif;

            if(auth()->user()->can('rep_bysections-list')):    

                $dpto = $this->section->join('departaments', 'section','=','sections_id')
                                  ->whereIn('departament', $deptoUser)
                                  ->where('departament','<>','')
                                  ->first(); 
                if($dpto):
                    $departaments =  $this->section->join('departaments', 'section','=','sections_id')
                                            ->where('section', $dpto->section)
                                            ->where('departament','<>','')
                                            ->get()
                                            ->groupBy('sections_id');

                    goto finish;
                endif;
            endif;
            
            if(auth()->user()->can('rep_bydepartaments-list')):
                if($deptoUser):
                    $departaments = $this->departament
                                        ->whereIn('departament', $deptoUser)
                                        ->where('departament','<>','')
                                        ->get()->groupBy('sections_id');
                    goto finish;
                endif;
            endif;
            
                $departaments = [];    
        
        finish:
        
        return view('reports.bydepartaments.index', compact('departaments'));
    }

   
    public function store(Request $request)
    {
        
        $start_datetime = $request->input('start_date').' '.$request->input('start_time');
        $end_datetime   = $request->input('end_date').' '.$request->input('end_time');
        $departaments = $request->input('departaments');
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
            'departaments' => 'required|min:1',
            'dialNumber' => 'nullable|numeric',
            'directions' => 'required|min:1',
            'types' => 'required|min:1',
        ]);
        
            $ext = $this->departament
                            ->leftJoin('extensions', 'extensions.departaments_id','=','departament')
                            ->whereIn('extensions.departaments_id',$departaments)
                            ->get();

            $acc = $this->departament
                            ->leftJoin('accountcodes', 'departaments_id','=','departament')
                            ->whereIn('departaments_id',$departaments)
                            ->get();
            
            $exts = $ext->map(function ($item, $key) {
                                return $item->extension;
                            });
                            
            $accs = $acc->map(function ($item, $key) {
                                return $item->accountcode;
                            });
                    
            $calls = $this->call->select('calls.id AS cid', (DB::raw(
                                    "(SELECT phonename FROM phonebooks WHERE callnumber LIKE CONCAT(phonenumber,'%') ORDER BY length(phonenumber) DESC LIMIT 1) AS phonename "
                                )),'*')
                                ->leftJoin('prefixes', 'prefixes_id', '=', 'prefix')
                                ->leftJoin('extensions', 'extensions_id', '=', 'extension')
                                ->leftJoin('accountcodes', 'accountcodes_id', '=', 'accountcode')
                                ->leftJoin('departaments', 'extensions.departaments_id', '=', 'departament','OR','accountcodes.departaments_id', '=', 'departament')
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
                                    'departament',
                                        function ($item) {
                                            return date('d/m/Y', strtotime($item->calldate));
                                        }
                                    ], $preserveKeys = true);
        
        //altera a localização para ajustar as datas para portugues brasil
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
                                
        //Inicio Relatorio.
        $this->bydepartament->AliasNbPages();
        if($report == 'detail'):
            //Insere um nome no cabeçalho
            $this->bydepartament->rName = trans('reports.d_departament');
        else:
            //Insere um nome no cabeçalho
            $this->bydepartament->rName = trans('reports.c_departament');
        endif;
        //Insere a data de emissao
        $this->bydepartament->rTitle = utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today')));
        //VARIAVEIS DE SOMARIZAÇÃO
        $SIN = 0; $STIN = 0; $SVIN = 0;
        $SOC = 0; $STOC = 0; $SVOC = 0;
        $SIC = 0; $STIC = 0; $SVIC = 0;
        $SLIN= 0;
        if($calls->count() !=0):                    
            
            foreach($calls as $e => $ds):
                $exten = $this->departament->where('departament',$e)->first();
                $this->bydepartament->rUser = $exten->departament;
                $this->bydepartament->rStart = $start_datetime;
                $this->bydepartament->rExten = $exten->sections_id;
                $this->bydepartament->rEnd   = $end_datetime;
                                    
                if($report == 'detail'):
                    // imprime cabeçalho da tabela
                    $this->bydepartament->rPrint   = true;
                else:
                    // Não imprime cabeçalho da tabela
                    $this->bydepartament->rPrint   = false;
                endif;
                $this->bydepartament->AddPage();
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
                            $this->bydepartament->Cell(60,19,  utf8_decode( ($c->accountcodes_id?'* ':'  ').date('d/m/Y', strtotime($c->calldate) ) ), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(60,19,  utf8_decode( date('H:i:s', strtotime($c->calldate) )), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(60,19,  utf8_decode( $c->extensions_id), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(120,19,  utf8_decode( $c->accountcodes_id ? $c->aname : $c->ename), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(65,19,  utf8_decode( trans('reports.'.$c->direction) ), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(60,19,  utf8_decode( $c->trunks_id), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(120,19, utf8_decode( substr($c->dialnumber,0,16)), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(150,19, utf8_decode( substr($c->locale,0,22) ), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(45,19,  utf8_decode( gmdate("H:i:s", $c->billsec) ), 0, 0, 'C', $true);
                            $this->bydepartament->Cell(50,19,  utf8_decode( number_format( $c->rate, 2, ',', '.') ), 0, 1, 'C', $true);
                        endif;                
                    endforeach;
                endforeach;
                                
                // verifica se a pagina contem mais de 16 linhas restantes para impressao do resumo dos ramais
                $footer = $lines+$line;
                if ( (($lines+$line) % 22) >= 16 || (($lines+$line) % 22) == 0):
                    $this->bydepartament->AddPage();
                endif;
                                
                // imprime o resumo das ligaçoes do ramal
                $this->bydepartament->SetY( 423 ); 
                $this->bydepartament->SetFillColor(224, 224, 224);
            
                $this->bydepartament->SetFont('arial', 'B', 11);
                $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
                $this->bydepartament->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
                $this->bydepartament->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
                $this->bydepartament->SetFont('arial', 'B', 11);
                $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
                $this->bydepartament->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
                $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bydepartament->SetFont('arial', 'B', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
                $this->bydepartament->setFont('arial', '', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(secHours(($TIN / ($IN==0?1:$IN) ))), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format(($VIN / ($IN==0?1:$IN) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode($IN), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode(secHours($TIN)), 0, 0, 'C', true);
                $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format($VIN, 2, ',', '.')), 0, 1, 'C', true);
                $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bydepartament->SetFont('arial', 'B', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
                $this->bydepartament->setFont('arial', '', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(secHours(($TIC / ($IC==0?1:$IC) ))), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format(($VIC / ($IC==0?1:$IC) ), 2, ',', '.')), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode($IC), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode(secHours($TIC)), 0, 0, 'C');
                $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format($VIC, 2, ',', '.')), 0, 1, 'C');
                $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bydepartament->SetFont('arial', 'B', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
                $this->bydepartament->setFont('arial', '', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(secHours(($TOC / ($OC==0?1:$OC) ))), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format(($VOC / ($OC==0?1:$OC) ), 2, ',', '.')), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode($OC), 0, 0, 'C', true);
                $this->bydepartament->Cell(65, 19, utf8_decode( secHours($TOC)), 0, 0, 'C', true);
                $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format($VOC, 2, ',', '.')), 0, 1, 'C', true);
                $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
                $this->bydepartament->SetFont('arial', 'B', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
                $this->bydepartament->setFont('arial', '', 11);
                $this->bydepartament->Cell(65, 19, utf8_decode(secHours(( ($TIN+$TIC+$TOC) / $lines)) ), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format( (($VIN+$VIC+$VOC) / $lines) , 2, ',', '.')), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode($lines), 0, 0, 'C');
                $this->bydepartament->Cell(65, 19, utf8_decode(secHours(($TIN+$TIC+$TOC))), 0, 0, 'C');
                $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format(($VIN+$VIC+$VOC), 2, ',', '.')), 0, 1, 'C');
                                    
                // variaveis para somarização total de todas as ligações
                $SIN  += $IN; $STIN += $TIN; $SVIN += $VIN;
                $SOC  += $OC; $STOC += $TOC; $SVOC += $VOC;
                $SIC  += $IC; $STIC += $TIC; $SVIC += $VIC;
                $SLIN += $lines;
            endforeach;
                                    
            // nao imprime o cabeçalho das tabelas
            $this->bydepartament->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->bydepartament->rUser  = trans('reports.finish');
            $this->bydepartament->rGroup = trans('reports.finish');
            $this->bydepartament->rStart = $start_datetime;
            $this->bydepartament->rExten = trans('reports.finish');
            $this->bydepartament->rDepto = trans('reports.finish');
            $this->bydepartament->rEnd   = $end_datetime;
            
            $this->bydepartament->AddPage();
            $this->bydepartament->SetY( 423 );                      
            $this->bydepartament->SetFillColor(224, 224, 224);
            
            $this->bydepartament->SetFont('arial', 'B', 11);
            $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.resume')), 0, 0, 'L', true);
            $this->bydepartament->Cell(130, 19, utf8_decode(trans('reports.medio')), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode(""), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'C', true);
            $this->bydepartament->Cell(67, 19, utf8_decode(""), 0, 1, 'C', true);
            
            $this->bydepartament->SetFont('arial', 'B', 11);
            $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.type')), 0, 0, 'L');
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.value')), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.qtd')), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.duraction')), 0, 0, 'C');
            $this->bydepartament->Cell(67, 19, utf8_decode(trans('reports.value')), 0, 1, 'C');
            $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bydepartament->SetFont('arial', 'B', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.internal')), 0, 0, 'L', true);
            $this->bydepartament->setFont('arial', '', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode( secHours( ($STIN / ($SIN==0?1:$SIN) ))), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIN / ($SIN==0?1:$SIN) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode($SIN), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode( secHours($STIN)), 0, 0, 'C', true);
            $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format($SVIN, 2, ',', '.')), 0, 1, 'C', true);
            $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bydepartament->SetFont('arial', 'B', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.incomining')), 0, 0, 'L');
            $this->bydepartament->setFont('arial', '', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode( secHours( ($STIC / ($SIC==0?1:$SIC) ))), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format(($SVIC / ($SIC==0?1:$SIC) ), 2, ',', '.')), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode($SIC), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode( secHours($STIC) ), 0, 0, 'C');
            $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format($SVIC, 2, ',', '.')), 0, 1, 'C');
            $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bydepartament->SetFont('arial', 'B', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.outgoing')), 0, 0, 'L', true);
            $this->bydepartament->setFont('arial', '', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode( secHours( ($STOC / ($SOC==0?1:$SOC) ) ) ), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format(($SVOC / ($SOC==0?1:$SOC) ), 2, ',', '.')), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode($SOC), 0, 0, 'C', true);
            $this->bydepartament->Cell(65, 19, utf8_decode( secHours($STOC)), 0, 0, 'C', true);
            $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format($SVOC, 2, ',', '.')), 0, 1, 'C', true);
            $this->bydepartament->Cell(393, 19, utf8_decode(""), 0, 0, 'L');
            
            $this->bydepartament->SetFont('arial', 'B', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode(trans('reports.total')), 0, 0, 'L');
            $this->bydepartament->setFont('arial', '', 11);
            $this->bydepartament->Cell(65, 19, utf8_decode(secHours( ($STIN+$STIC+$STOC) / $SLIN) ), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode("R$ ". number_format( (($SVIN+$SVIC+$SVOC) / $SLIN) , 2, ',', '.')), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode($SLIN), 0, 0, 'C');
            $this->bydepartament->Cell(65, 19, utf8_decode(secHours($STIN+$STIC+$STOC)), 0, 0, 'C');
            $this->bydepartament->Cell(67, 19, utf8_decode("R$ ". number_format(($SVIN+$SVIC+$SVOC), 2, ',', '.')), 0, 1, 'C');
                                
        else:
            //NÃO HA DADOS
            $this->bydepartament->rPrint = false;
            // envia as variaveis referentes ao cabeçalho
            $this->bydepartament->rUser   = trans('reports.empty');
            $this->bydepartament->rGroup  = trans('reports.empty');
            $this->bydepartament->rStart  = $start_datetime;
            $this->bydepartament->rExten  = trans('reports.empty');
            $this->bydepartament->rDepto  = trans('reports.empty');
            $this->bydepartament->rEnd    = $end_datetime;
            
            $this->bydepartament->AddPage();

        endif;

        $file = str_replace(' ','_', auth()->user()->name.' '.trans('reports.d_exten').'.pdf');
        $this->bydepartament->Output($file,'F');
        $url = asset($file);
                          
        return view('reports.bydepartaments.show', compact('url', 'file'));
    }



}
