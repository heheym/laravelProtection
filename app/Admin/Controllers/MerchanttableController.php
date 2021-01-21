<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Merchanttable;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

class MerchanttableController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商户管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Merchanttable);

//        $grid->column('id', __('Id'));
        $grid->column('mtype', __('商户类型'))->display(function($mtype){
            return [0=>'场所类型',1=>'代理商类型',2=>'权利人类型'][$mtype];
        });
        $grid->column('merchantType', __('商户入驻类型'))->display(function($merchantType){
            return [1=>'小微',2=>'个体',3=>'企业'][$merchantType];
        });
        $grid->column('merchantId', __('商户编号'));
        $grid->column('merchantShortName', __('商户简称'));
        $grid->column('merchantName', __('商户全称'));
        /*$grid->column('servicePhone', __('客服电话'));
        $grid->column('mccCode', __('MccCode'));
        $grid->column('legalName', __('LegalName'));
        $grid->column('credentialType', __('CredentialType'));
        $grid->column('idCardno', __('IdCardno'));
        $grid->column('idcardFrontPic', __('IdcardFrontPic'));
        $grid->column('idcardBackPic', __('IdcardBackPic'));
        $grid->column('idcardHandPic', __('IdcardHandPic'));
        $grid->column('idCardStart', __('IdCardStart'));
        $grid->column('idCardEnd', __('IdCardEnd'));
        $grid->column('name', __('Name'));
        $grid->column('mobile', __('Mobile'));
        $grid->column('provinceCode', __('ProvinceCode'));
        $grid->column('cityCode', __('CityCode'));
        $grid->column('areaCode', __('AreaCode'));
        $grid->column('address', __('Address'));
        $grid->column('license', __('License'));
        $grid->column('licenseFullName', __('LicenseFullName'));
        $grid->column('licenseAddress', __('LicenseAddress'));
        $grid->column('licenseStart', __('LicenseStart'));
        $grid->column('licenseEnd', __('LicenseEnd'));
        $grid->column('licensePic', __('LicensePic'));
        $grid->column('type', __('Type'));
        $grid->column('legalFlag', __('LegalFlag'));
        $grid->column('unionpay', __('Unionpay'));
        $grid->column('holder', __('Holder'));
        $grid->column('aidCardNo', __('AidCardNo'));
        $grid->column('aidCardType', __('AidCardType'));
        $grid->column('amobile', __('Amobile'));
        $grid->column('bankCardNo', __('BankCardNo'));
        $grid->column('bankCardFrontPic', __('BankCardFrontPic'));
        $grid->column('nonLegSettleAuthPic', __('NonLegSettleAuthPic'));
        $grid->column('nonLegIdcardFrontPic', __('NonLegIdcardFrontPic'));
        $grid->column('nonLegIdcardBackPic', __('NonLegIdcardBackPic'));
        $grid->column('shopType', __('ShopType'));
        $grid->column('headMerchantId', __('HeadMerchantId'));
        $grid->column('settleTo', __('SettleTo'));
        $grid->column('insidePic', __('InsidePic'));
        $grid->column('doorPic', __('DoorPic'));
        $grid->column('cashierDeskPic', __('CashierDeskPic'));
        $grid->column('userWx', __('UserWx'));
        $grid->column('subAppid', __('SubAppid'));
        $grid->column('jsapiPath', __('JsapiPath'));
        $grid->column('subscribeAppid', __('SubscribeAppid'));
        $grid->column('wxMchName', __('WxMchName'));
        $grid->column('reportConfigId', __('ReportConfigId'));
        $grid->column('notifyAddress', __('NotifyAddress'));
        $grid->column('agreementPic', __('AgreementPic'));*/

        $grid->actions(function ($actions) {
            $actions->disableView();
            if (!Admin::user()->can('商户管理删除')) {
                $actions->disableDelete();
            }
            if (!Admin::user()->can('商户管理修改')) {
                $actions->disableEdit();
            }
        });
        if (!Admin::user()->can('商户管理添加')) {
            $grid->disableCreateButton();  //场所添加的权限
        }

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Merchanttable::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('mtype', __('Mtype'));
        $show->field('merchantType', __('MerchantType'));
        $show->field('merchantId', __('MerchantId'));
        $show->field('merchantShortName', __('MerchantShortName'));
        $show->field('merchantName', __('MerchantName'));
        $show->field('servicePhone', __('ServicePhone'));
        $show->field('mccCode', __('MccCode'));
        $show->field('legalName', __('LegalName'));
        $show->field('credentialType', __('CredentialType'));
        $show->field('idCardno', __('IdCardno'));
        $show->field('idcardFrontPic', __('IdcardFrontPic'));
        $show->field('idcardBackPic', __('IdcardBackPic'));
        $show->field('idcardHandPic', __('IdcardHandPic'));
        $show->field('idCardStart', __('IdCardStart'));
        $show->field('idCardEnd', __('IdCardEnd'));
        $show->field('name', __('Name'));
        $show->field('mobile', __('Mobile'));
        $show->field('provinceCode', __('ProvinceCode'));
        $show->field('cityCode', __('CityCode'));
        $show->field('areaCode', __('AreaCode'));
        $show->field('address', __('Address'));
        $show->field('license', __('License'));
        $show->field('licenseFullName', __('LicenseFullName'));
        $show->field('licenseAddress', __('LicenseAddress'));
        $show->field('licenseStart', __('LicenseStart'));
        $show->field('licenseEnd', __('LicenseEnd'));
        $show->field('licensePic', __('LicensePic'));
        $show->field('type', __('Type'));
        $show->field('legalFlag', __('LegalFlag'));
        $show->field('unionpay', __('Unionpay'));
        $show->field('holder', __('Holder'));
        $show->field('aidCardNo', __('AidCardNo'));
        $show->field('aidCardType', __('AidCardType'));
        $show->field('amobile', __('Amobile'));
        $show->field('bankCardNo', __('BankCardNo'));
        $show->field('bankCardFrontPic', __('BankCardFrontPic'));
        $show->field('nonLegSettleAuthPic', __('NonLegSettleAuthPic'));
        $show->field('nonLegIdcardFrontPic', __('NonLegIdcardFrontPic'));
        $show->field('nonLegIdcardBackPic', __('NonLegIdcardBackPic'));
        $show->field('shopType', __('ShopType'));
        $show->field('headMerchantId', __('HeadMerchantId'));
        $show->field('settleTo', __('SettleTo'));
        $show->field('insidePic', __('InsidePic'));
        $show->field('doorPic', __('DoorPic'));
        $show->field('cashierDeskPic', __('CashierDeskPic'));
        $show->field('userWx', __('UserWx'));
        $show->field('subAppid', __('SubAppid'));
        $show->field('jsapiPath', __('JsapiPath'));
        $show->field('subscribeAppid', __('SubscribeAppid'));
        $show->field('wxMchName', __('WxMchName'));
        $show->field('reportConfigId', __('ReportConfigId'));
        $show->field('notifyAddress', __('NotifyAddress'));
        $show->field('agreementPic', __('AgreementPic'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Merchanttable);

        $form->column(5/6, function ($form) {
            $form->select('mtype', __('商户类型'))->options([0=>'场所类型',1=>'代理商类型',2=>'权利人类型']);
            $form->select('merchantType', __('商户入驻类型'))->options([1=>'小微',2=>'个体',3=>'企业'])->default(1);
            $form->text('merchantId', __('商户编号'));
            $form->text('merchantShortName', __('商户简称'));
            $form->text('merchantName', __('商户全称'));
        });
        /*$form->text('servicePhone', __('ServicePhone'));
        $form->text('mccCode', __('MccCode'));
        $form->text('legalName', __('LegalName'));
        $form->switch('credentialType', __('CredentialType'))->default(1);
        $form->text('idCardno', __('IdCardno'));
        $form->text('idcardFrontPic', __('IdcardFrontPic'));
        $form->text('idcardBackPic', __('IdcardBackPic'));
        $form->text('idcardHandPic', __('IdcardHandPic'));
        $form->text('idCardStart', __('IdCardStart'));
        $form->text('idCardEnd', __('IdCardEnd'));
        $form->text('name', __('Name'));
        $form->mobile('mobile', __('Mobile'));
        $form->text('provinceCode', __('ProvinceCode'));
        $form->text('cityCode', __('CityCode'));
        $form->text('areaCode', __('AreaCode'));
        $form->text('address', __('Address'));
        $form->text('license', __('License'));
        $form->text('licenseFullName', __('LicenseFullName'));
        $form->text('licenseAddress', __('LicenseAddress'));
        $form->text('licenseStart', __('LicenseStart'));
        $form->text('licenseEnd', __('LicenseEnd'));
        $form->text('licensePic', __('LicensePic'));
        $form->switch('type', __('Type'));
        $form->switch('legalFlag', __('LegalFlag'));
        $form->text('unionpay', __('Unionpay'));
        $form->text('holder', __('Holder'));
        $form->text('aidCardNo', __('AidCardNo'));
        $form->switch('aidCardType', __('AidCardType'))->default(1);
        $form->text('amobile', __('Amobile'));
        $form->text('bankCardNo', __('BankCardNo'));
        $form->text('bankCardFrontPic', __('BankCardFrontPic'));
        $form->text('nonLegSettleAuthPic', __('NonLegSettleAuthPic'));
        $form->text('nonLegIdcardFrontPic', __('NonLegIdcardFrontPic'));
        $form->text('nonLegIdcardBackPic', __('NonLegIdcardBackPic'));
        $form->switch('shopType', __('ShopType'))->default(1);
        $form->text('headMerchantId', __('HeadMerchantId'));
        $form->switch('settleTo', __('SettleTo'));
        $form->text('insidePic', __('InsidePic'));
        $form->text('doorPic', __('DoorPic'));
        $form->text('cashierDeskPic', __('CashierDeskPic'));
        $form->text('userWx', __('UserWx'));
        $form->text('subAppid', __('SubAppid'));
        $form->text('jsapiPath', __('JsapiPath'));
        $form->text('subscribeAppid', __('SubscribeAppid'));
        $form->text('wxMchName', __('WxMchName'));
        $form->number('reportConfigId', __('ReportConfigId'));
        $form->text('notifyAddress', __('NotifyAddress'));
        $form->text('agreementPic', __('AgreementPic'));*/

        return $form;
    }
}
