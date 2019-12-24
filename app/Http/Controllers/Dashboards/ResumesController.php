<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Call;
use App\Models\Prefix;



class ResumesController extends Controller
{
    public function __construct(Call $call, Prefix $prefix)
    {
        $this->call = $call;        
        $this->prefix = $prefix;        
    }

    public function index(Request $request)
    {
        $months =(object)['01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril','05'=>'Maio','06'=>'Junho','07'=>'Julho','08'=>'Agosto','09'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro'];
        $m = $request->month?$request->month:date('m'); 
        $e = $request->exten?$request->exten:'all'; 
        $extens = $this->call->select('extensions_id')->distinct('extensions_id')->get();
        $y = date('Y');
        $cdrs='';

        if($e == 'all'):
            foreach($extens as $exten):
                $extensions_id[] = $exten->extensions_id;
            endforeach;
        else:
            $extensions_id[] = $e;
        endif;
        
        $data=(object)[
            'time'  => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
            'qtd'   => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
            'val'   => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
            'hour'  => [
                        '00' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '01' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '02' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '03' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '04' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '05' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '06' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '07' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '08' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '09' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '10' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '11' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '12' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '13' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '14' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '15' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '16' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '17' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '18' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '19' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '20' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '21' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '22' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],
                        '23' => ['vc1'=> 0,'vc2'=> 0,'vc3'=> 0,'ddi'=> 0,'ddd'=> 0,'local'=> 0,'gratuito'=> 0,'serviços'=> 0,'outros'=> 0,'tie_line'=> 0,'total'=> 0,'in'=> 0,'oc'=> 0,'ic'=> 0, 'interno'=> 0],          
                    ]       
        ];
        
        $cdrs = $this->call->whereMonth('calldate',$m)->whereYear('calldate',$y)->whereIn('extensions_id',$extensions_id)->where('status_id','1')
                            ->get();
        //$days = $this->call->selectRaw('distinctic day(date) Day')->whereMonth('calldate',$m)->whereYear('calldate',$y)->whereIn('extensions_id',$extensions_id)->where('status_id','1')
        //              ->count();
        //dd($days);
        
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
        $days = $cdrs->groupBy( function ($item){
                                return date('d',strtotime( $item->calldate ));
                            })->count();
        //dd($days);

        $data = json_decode(json_encode($data));
        
        return view('dashboards.resumes.index', compact('data', 'months','m','extens','e', 'days'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
