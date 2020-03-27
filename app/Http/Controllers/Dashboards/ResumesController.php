<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Call;
use App\Models\Pbx;
use App\Models\Prefix;
use App\Models\Extension;
use App\Models\Accountcode;


class ResumesController extends Controller
{
    public function __construct(Call $call, Prefix $prefix, Pbx $pbx, Extension $extension, Accountcode $accountcode)
    {
        $this->call = $call;        
        $this->pbx = $pbx;        
        $this->prefix = $prefix;        
        $this->extension = $extension;
        $this->accountcode = $accountcode;
                
    }

    public function index(Request $request)
    {
        $ALL = '';
        $extensions='';
        $authUser = auth()->user()->email;
        $extenUser = $this->extension->where('users_id',$authUser)->first();
        $accUser = $this->accountcode->where('users_id',$authUser)->first();
        
        if(auth()->user()->can('cfg_groups-list')):
            $pbxes = $this->pbx->select('name')->where('name',$extenUser->pbxes_id)->get();
            $extensions = $this->extension->where('groups_id', $extenUser->groups_id )->get()->groupBy('pbxes_id');
            $ALL = $this->extension->select('extension')->where('groups_id', $extenUser->groups_id)->get();
        
        elseif(auth()->user()->can('cfg_tenants-list')):
            //$dataUsers = $this->extension->where('groups_id', $extenUser->groups_id);

        elseif(auth()->user()->can('cfg_sections-list')):
        
        elseif(auth()->user()->can('cfg_departaments-list')):
        
        elseif(auth()->user()->can('cfg_extensions-list')):
            $pbxes = $this->pbx->select('name')->where('name',$extenUser->pbxes_id)->get();
            $extensions = $this->extension->where('users_id', $authUser)->get()->groupBy('pbxes_id');
            $ALL = $this->extension->where('users_id', $authUser)->get();
        
        elseif(auth()->user()->can('cfg_accountcodes-list')):
            $pbxes = $this->pbx->select('name')->where('name',$accUser->pbxes_id)->get();
            $extensions = $this->accountcode->select('accountcode AS extension','aname  AS ename', '*')->where('users_id', $authUser)->get()->groupBy('pbxes_id');
            $ALL = $this->accountcode->where('users_id', $authUser)->get();
            
        else:
        
        endif;

        $data=(object)[
            'time'  => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
            'qtd'   => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
            'val'   => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
            'hour'  => [
                        '00' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '01' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '02' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '03' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '04' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '05' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '06' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '07' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '08' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '09' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '10' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '11' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '12' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '13' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '14' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '15' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '16' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '17' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '18' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '19' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '20' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '21' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '22' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],
                        '23' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ldi'=> 0,'ldn'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0, 'int'=> 0],          
                    ]       
        ];
        
        $months =(object)['01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril','05'=>'Maio','06'=>'Junho','07'=>'Julho','08'=>'Agosto','09'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro'];
        $m = $request->month?$request->month:date('m'); 
        $e = $request->exten?$request->exten:'all'; 
        $p = $request->pbxes?$request->pbxes:'all'; 
        //$pbxes = $this->pbx->select('name')->get();
        //$extensions = $this->extension->all()->groupBy('pbxes_id');
        $y = date('Y');
        $cdrs='';
        
        if($ALL == '' || $extensions ==''):
            $extensions=[];
            $days='1';
            $pbxes=[];
        
        else:
            if($e == 'all'):
                $all = $ALL;
                //$all = $this->extension->select('extension')->get();
                $extensions_id = $all->pluck('extension')->all();
            else:
                $extensions_id[] = $e;
            endif;

            if($p == 'all'):
                $pbxs = $pbxes->pluck('name')->all();
            else:
                $pbxs[] = $p;
            endif;
            
            $cdrs = $this->call->whereMonth('calldate',$m)
                                ->whereYear('calldate',$y)
                                ->whereIn('pbx',$pbxs)
                                ->whereIn('extensions_id',$extensions_id)
                                ->where('status_id','1')
                                ->get();
            
            $days = $this->call->selectRaw('count(distinct date(calldate))')
                                ->whereMonth('calldate',$m)
                                ->whereYear('calldate',$y)
                                ->whereIn('pbx',$pbxs)
                                ->whereIn('extensions_id',$extensions_id)
                                ->where('status_id','1')
                                ->first();
            $days = $days->count?$days->count:1;
        
            //dd($cdrs)
            foreach($cdrs as $cdr):
                
                if($cdr->direction == 'OC'):
                    $data->time[strtolower($cdr->cservice?$cdr->cservice:'interno')] += $cdr->billsec;
                    $data->val[strtolower($cdr->cservice ?$cdr->cservice:'interno')] += $cdr->rate;
                    $data->qtd[strtolower($cdr->cservice ?$cdr->cservice:'interno')] ++;
                endif;

                $data->time[strtolower($cdr->direction)] += $cdr->billsec;
                $data->val[strtolower($cdr->direction)] += $cdr->rate;
                $data->qtd[strtolower($cdr->direction)] ++;
                
                $data->time['total'] += $cdr->billsec;
                $data->val['total'] += $cdr->rate;
                $data->qtd['total'] ++;
                $data->hour[date('H',strtotime( $cdr->calldate ))]['total']++;
                $data->hour[date('H',strtotime( $cdr->calldate ))][strtolower($cdr->direction)] ++;
                
                $data->hour[date('H',strtotime( $cdr->calldate ))][strtolower($cdr->cservice ?$cdr->cservice:'interno')]++;
            
            endforeach;
        
            
        endif;
        
        $data = json_decode(json_encode($data));
        return view('dashboards.resumes.index', compact('data', 'months','m','e', 'days','pbxes','p','extensions'));
    }

    
    
}
