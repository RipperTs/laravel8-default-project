<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Setting;
use App\Http\Services\SettingService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Illuminate\Support\Facades\Cache;

class SettingController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->title('系统设置')
            ->description('编辑')
            ->body($this->form());
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Setting(), function (Form $form) {
            $setting = SettingService::getItem('system');
            $form->action('setting');
            // 设置标题
            $form->title('基本设置');

            $form->hidden('name', '系统名称')->value($setting['name'] ?? 'Dcat Admin')->required();
            $form->text('wechat_app_id', '小程序AppID')->value($setting['wechat_app_id'] ?? '')->required();
            $form->password('wechat_app_secret', '小程序Secret')->value($setting['wechat_app_secret'] ?? '')->required();
            $form->text('wechat_payment_mchid', '商户号')
                ->value($setting['wechat_payment_mchid'] ?? '')
                ->help('微信支付商户号,mchid');
            $form->password('wechat_payment_key', '支付秘钥')
                ->value($setting['wechat_payment_key'] ?? '')
                ->help('微信支付V2版本秘钥');
            $form->textarea('wechat_certificate_cert', '支付证书(PEM格式)')->value($setting['wechat_certificate_cert'] ?? '')
                ->help('微信支付证书文件内容, apiclient_cert.pem');
            $form->textarea('wechat_certificate_key', '支付证书秘钥(KEY)')->value($setting['wechat_certificate_key'] ?? '')
                ->help('微信支付证书文件内容, apiclient_key.pem');

            $form->rate('withdrawal_fee', '提现手续费')
                ->value($setting['withdrawal_fee'] ?? '');

            $form->submitted(function (Form $form) {
                // 获取用户提交参数
                $data = $form->input();
                unset($data['_previous_']);
                unset($data['_token']);
                \App\Models\Setting::query()->where('key', 'system')->update([
                    'values' => $data
                ]);
                if ($data['wechat_certificate_cert'] && $data['wechat_certificate_key']) {
                    // 写入证书目录文件
                    file_put_contents(public_path() . '/certificate/apiclient_cert.pem', $data['wechat_certificate_cert']);
                    file_put_contents(public_path() . '/certificate/apiclient_key.pem', $data['wechat_certificate_key']);
                }
                Cache::delete('setting_10001');
                return $form->response()->success('操作成功');
            });

            $form->disableListButton();
            $form->disableResetButton();
            $form->disableCreatingCheck();
            $form->disableEditingCheck();
            $form->disableViewCheck();
        });
    }
}
