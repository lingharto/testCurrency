<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;

class UpdateCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:currency {char_code?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update one or all currencies';

    protected $actualCurrency;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->getActualCurrency();
    }

    private function getActualCurrency()
    {
        $xmlstring = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp');
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $this->actualCurrency = $array['Valute'];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if($this->argument('char_code')) {
            $currency = Currency::where('char_code', $this->argument('char_code'))->first();
            if(!$currency) {
                $currency = new Currency();
            }
            foreach ($this->actualCurrency as $actualCurrency) {
                if($actualCurrency['CharCode'] == $this->argument('char_code')) {
                    $currency->char_code = $actualCurrency['CharCode'];
                    $currency->name = $actualCurrency['Name'];
                    $actualCurrency['Value'] = str_replace(',', '.', $actualCurrency['Value']);
                    $currency->rate = (float)$actualCurrency['Value'] / (int)$actualCurrency['Nominal'];
                    $currency->save();
                }
            }
        } else {
            foreach ($this->actualCurrency as $actualCurrency) {
                $currency = Currency::where('char_code', $actualCurrency['CharCode'])->first();
                if(!$currency) {
                    $currency = new Currency();
                }
                $currency->char_code = $actualCurrency['CharCode'];
                $currency->name = $actualCurrency['Name'];
                $actualCurrency['Value'] = str_replace(',', '.', $actualCurrency['Value']);
                $currency->rate = (float)$actualCurrency['Value'] / (int)$actualCurrency['Nominal'];
                $currency->save();
            }
        }
    }
}
