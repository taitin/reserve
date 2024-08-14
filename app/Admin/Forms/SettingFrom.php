<?php

namespace App\Admin\Forms;

use App\Models\Setting;
use Dcat\Admin\Form\Field\Image;
use Dcat\Admin\Widgets\Form;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingFrom extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $request)
    {
        $setting = Setting::where('key',   $this->key)->first();
        if (empty($setting)) {
            $setting = new Setting();
            $setting->key =   $this->key;
        }
        $ret =  $this->dataPrepare($request, $setting->value);

        if (!isset($ret['result']) || $ret['result'] == false) {
            return    back()->withErrors($ret['error'])->withInput($ret['input']);
        }
        $setting->value = $ret['data'];
        $setting->save();


        return $this
            ->response()
            ->success(__('admin.update_succeeded'))
            ->refresh();
    }


    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        $setting = Setting::where('key',   $this->key)->first();
        if (!empty($setting))    return $setting->value;
        else return [];
    }

    public function dataPrepare($data, $origin)
    {
        // return  $this->withMultipleHandle( $request, $origin);



        // for ($i = 1; $i <= 5; $i++) {
        //     if (!isset($data['image_' . $i]))    $data['image_' . $i] = $origin['image_' . $i];
        //     else {
        //         $image = new Image('image');;
        //         $image->uniqueName();
        //         $data['image_' . $i] = $image->prepare($data['image_' . $i]);
        //     }
        // }
        return  ['result' => true, 'data' => $data];
    }
}
