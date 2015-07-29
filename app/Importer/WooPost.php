<?php

namespace App\Importer;

use Illuminate\Database\Eloquent\Model;

class WooPost extends Model
{
    protected $connection = 'wordpress';
    protected $table = 'wp_posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;
}
