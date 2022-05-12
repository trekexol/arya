<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IndexBcv extends Model
{
    protected $connection = 'logins';

    protected $table = 'bvc_rates_social_benefits';
}
