<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Faker\Generator as Faker;
use App\Models\Device;
use App\Models\Order;

class FakeOrder extends Command
{
    protected $faker;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fake-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Faker $faker)
    {
        parent::__construct();
        $this->faker = $faker;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $order = Order::create([
            'title' => $this->faker->sentence(5),
            'content' => $this->faker->paragraph(),
            'shipper_id' => null
        ]);

        $devices = Device::pluck('device_secret')->all();

        try {
            $this->sendNotification($order, $devices);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function sendNotification($order, $devices)
    {
        $message = [
            'title' => $order->title,
            'body' => $order->content,
            'sound' => true,
        ];

        $notification = [
            'registration_ids' => $devices,
            'notification' => $message,
        ];

        $headers = [
            'Authorization: key=AIzaSyCH7mV96YbGpn2w8OhLxmB0v-JOrcoGxtE',
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));

        $result = curl_exec($ch);

        curl_close($ch);

        if ($result === FALSE) {
            throw new Exception('FCM Send Error: '  .  curl_error($ch), 500);
        }

        return $result;
    }
}
