class Payment extends Model
{
    protected $fillable = [
        'laundry_order_id',
        'user_id',
        'method',
        'amount_paid',
        'points_used',
        'paid_at',
    ];

    public function order()
    {
        return $this->belongsTo(
            LaundryOrder::class,
            'laundry_order_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}