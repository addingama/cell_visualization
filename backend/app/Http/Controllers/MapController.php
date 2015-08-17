<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Data;
use App\Phone;
use App\Pivot;
use App\DataBali;
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
        $phones = Data::select('MSISDN')
                        ->get();

        $result = [];
        foreach ($phones as $key => $value) {
            array_push($result, $value->MSISDN);
        }

        return response()->json($result);
    }

    public function filterNumber(Request $request, $date, $number) {
        $result[$number] = Data::select('Tanggal', 'Jam', 'Lat', 'Long',  'MSISDN')
                            ->where('tanggal', '=', $date)
                            ->where('MSISDN', '=', $number)
                            ->groupBy('Lat')
                            ->orderBy('Jam', 'ASC')
                            ->get();

        return $result;
    }

    public function filterRange(Request $request, $date, $numbers, $start) {
        $phones = Phone::select('MSISDN')
                    ->skip($start)
                    ->take($numbers)
                    ->get();

        $p = [];
        foreach ($phones as $key => $value) {
            array_push($p, $value->MSISDN);
        }

        $data = Data::select('Tanggal', 'Jam', 'Lat', 'Long', 'MSISDN')
                ->whereIn('MSISDN', $p)
                ->groupBy('Lat')
                ->orderBy('Jam', 'ASC')
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

    public function generatePivot(Request $request, $numbers, $start) {
        // SELECT maps.`data_bali`.`Kec` AS `Kecamatan`, COUNT(maps.`data_bali`.`Kec`) AS `jumlah` FROM maps.`data_bali` WHERE `MSISDN` = '628111000225' GROUP BY maps.`data_bali`.`Kec`;
        $phones = DataBali::skip($start)
                        ->take($numbers)
                        ->get();

        // kosongkan tabel pivot
        // Pivot::truncate();
        DB::beginTransaction();
        try {
            foreach ($phones as $key => $value) {
                $phone = Pivot::select('MSISDN')
                            ->where('MSISDN', '=', $value->MSISDN)
                            ->first();

                if ($phone == null) {
                    $p = new Pivot();
                    $p->MSISDN = $value->MSISDN;
                } else {
                    $p = Pivot::find($value->MSISDN);
                }
                $kec = $value->Kec;
                $p->$kec = $p->$kec + 1;
                $p->save(); 
            }

            $result = [
                'message' => 'DONE Pivoting ' . $numbers . ' data start from ' . $start 
            ];
        } catch (Exception $e) {
            DB::rollback();
            $result = [
                'message' => 'Error rolling back' 
            ];
        }
        DB::commit();
        return response()->json($result);
    }
}
