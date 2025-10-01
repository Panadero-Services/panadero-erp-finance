<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ErpCustomer;
use App\Models\ErpSupplier;
use App\Models\ErpProduct;
use App\Models\ErpOrdersIn;
use App\Models\ErpOrdersOut;
use App\Models\ErpOrderInLine;
use App\Models\ErpOrderOutLine;
use App\Models\ErpSite;
use App\Models\ErpStorage;
use App\Models\ErpStock;
use App\Models\ErpStatus;
use App\Models\ErpUnit;
use App\Models\ErpProductType;
use App\Models\ErpEventHandler;
use App\Models\ErpStockHandler;
use App\Models\ErpSample;
use App\Models\ErpAnalysis;
use App\Models\ErpAnalyseProperty;
use App\Models\ErpAnalyseLine;

class ErpDataController extends Controller
{
    public function customers()
    {
        return response()->json(ErpCustomer::all());
    }

    public function suppliers()
    {
        return response()->json(ErpSupplier::all());
    }

    public function products()
    {
        return response()->json(ErpProduct::with(['productType', 'productGroup', 'brand', 'unit'])->get());
    }

    public function ordersIn()
    {
        return response()->json(ErpOrdersIn::all());
    }

    public function ordersOut()
    {
        return response()->json(ErpOrdersOut::all());
    }

    public function orderInLines()
    {
        return response()->json(ErpOrderInLine::all());
    }

    public function orderOutLines()
    {
        return response()->json(ErpOrderOutLine::all());
    }

    public function sites()
    {
        return response()->json(ErpSite::all());
    }

    public function storages()
    {
        return response()->json(ErpStorage::all());
    }

    public function stocks()
    {
        return response()->json(ErpStock::all());
    }

    public function status()
    {
        return response()->json(ErpStatus::all());
    }

    public function units()
    {
        return response()->json(ErpUnit::all());
    }

    public function productTypes()
    {
        return response()->json(ErpProductType::all());
    }

    public function eventHandlers()
    {
        return response()->json(ErpEventHandler::all());
    }

    public function stockHandlers()
    {
        return response()->json(ErpStockHandler::all());
    }

    public function samples()
    {
        return response()->json(ErpSample::all());
    }

    public function analyses()
    {
        return response()->json(ErpAnalysis::all());
    }

    public function analyseProperties()
    {
        return response()->json(ErpAnalyseProperty::all());
    }

    public function analyseLines()
    {
        return response()->json(ErpAnalyseLine::all());
    }
}