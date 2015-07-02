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
                    ->select('Tanggal', 'Jam', 'Lat', 'Long',  'MSISDN')
                    ->where('tanggal', '=', $date)
                    ->where('MSISDN', '=', $number)
                    ->groupBy('Lat')
                    // ->orderBy('Jam', 'ASC')
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
                ->select('Tanggal', 'Jam', 'Lat', 'Long', 'MSISDN')
                ->whereIn('MSISDN', $p)
                ->groupBy('Lat')
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
