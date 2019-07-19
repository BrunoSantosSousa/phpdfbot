<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Opportunity
 * @package App\Models
 *
 * @property string $id
 * @property string $title
 * @property string $position
 * @property string $description
 * @property string $salary
 * @property string $company
 * @property string $location
 * @property int $telegram_id
 * @property int $status
 * @property Collection $files
 */
class Opportunity extends Model
{

    use SoftDeletes;

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    protected $fillable = [
        'title',
        'position',
        'description',
        'salary',
        'company',
        'location',
        'files',
        'telegram_id',
        'status',
    ];

    protected $guarded = ['id'];

    /**
     * @var Collection
     */
    private $filesArray;

    public function __construct(array $attributes = [])
    {
        $this->initFiles();
        parent::__construct($attributes);
    }

    public function initFiles()
    {
        $this->filesArray = new Collection();
    }

    /**
     * @return Collection
     */
    public function getFilesList(): Collection
    {
        if (empty($this->filesArray) || !$this->filesArray->isNotEmpty()) {
            $this->filesArray = $this->getFilesAttribute();
        }
        return $this->filesArray;
    }

    public function addFile(string $file)
    {
        $this->filesArray->add($file);
    }

    public function hasFile(): bool
    {
        return $this->filesArray ? $this->filesArray->isNotEmpty() : false;
    }

    public function getFilesAttribute(): Collection
    {
        if (is_string($this->files) && strlen($this->files) > 0) {
            return collect(json_decode($this->files));
        }
        return $this->files;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function(Opportunity $opportunity)
        {
            $opportunity->files = optional($opportunity->filesArray)->toJson();
        });

        static::updating(function(Opportunity $opportunity)
        {
            $opportunity->files = optional($opportunity->filesArray)->toJson();
        });
    }
}
