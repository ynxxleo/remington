<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Copier extends Model
{
    use HasFactory;
    
    protected $table = 'copier';
    
    public function copier()
    {
    	return $this->belongsTo(User::class, 'copier');
    }
    
    public function master()
    {
    	return $this->belongsTo(User::class, 'master');
    }

}
