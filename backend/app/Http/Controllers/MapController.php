<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use DB;

class MapController extends Controller
{

    public function home() {

        $result = [
            'message' => 'Your api server working'
        ];
        return response()->json($result);
    }

    public function index(Request $request) {
        $query = 'select * from data';
        if ($request->has('type')) {
            if ($request->input('type') == 'sql') {
                $query = $request->input('data');
            } else {
                $query = 'select * from data ';

                if ($request->has('column') && $request->has('data') && $request->has('operator')) {
                    $query .= ' where ' . $request->input('column') . ' ' . $request->input('operator') . ' "' . $request->input('data') . '"  ';
                }
                $query .= ' order by JAM ASC ';
                if ($request->has('limit')) {
                    $query .= ' limit ' . $request->input('limit');
                }
            }
        }
        $result = DB::select($query);
        return response()->json($result);
    }

    public function getDates(Request $request) {
        $dates = DB::table('data')
                    ->select('tanggal')
                    ->groupBy('tanggal')
                    ->get();

        return $dates;
    }
    public function getPhoneByDate(Request $request, $date) {
        $dates = DB::table('data')
                    ->select('MSISDN')
                    ->where('tanggal', '=', $date)
                    ->groupBy('MSISDN')
                    ->get();

        return count($dates);
    }

    public function getPhones(Request $request) {
        $phones = DB::table('numbers')
                    ->select('*')
                    ->get();

        $result = [];
        foreach ($phones as $key => $value) {
            array_push($result, $value->MSISDN);
        }

        return $result;
    }

    public function filterNumber(Request $request, $date, $number) {
        $result[$number] = DB::table('data')
                    ->select('*')
                    ->where('tanggal', '=', $date)
                    ->where('MSISDN', '=', $number)
                    ->get();


        return $result;
    }

    public function filterRange(Request $request, $date, $numbers, $start) {
        $phones = DB::table('numbers')
                    ->select('*')
                    ->skip($start)
                    ->take($numbers)
                    ->get();

        $p = [];
        foreach ($phones as $key => $value) {
            array_push($p, $value->MSISDN);
        }

        $data = DB::table('data')
                ->whereIn('MSISDN', $p)
                ->get();

        // prepare result array
        foreach ($p as $value) {
            $result[$value] = [];
        }
        foreach ($data as $key => $value) {
            array_push($result[$value->MSISDN], $value);
        }

        return $result;
    }
}
