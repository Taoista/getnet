<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerPage extends Model{
    use HasFactory;
    protected $table = "per_page"; 
    public $timestamps = false;
}
