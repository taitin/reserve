<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class PortalUser extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'portal_users';
    
}
