<?php

namespace App\Models;

use App\Models\ArticleGroup;
use App\Models\SaleDetail;
use App\Models\RentalDetail;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    public function group() {
        return $this->belongsTo(ArticleGroup::class, 'article_group_id');
    }

    public function saleDetails() {
        return $this->hasOne(SaleDetail::class);
    }

    public function rentalDetails() {
        return $this->hasOne(RentalDetail::class);
    }

}
