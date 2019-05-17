<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
 jQuery(document).on('gform_post_render', init_company());
 
 
init_company = function() {
   (function(d,b,a,s,e){ var t = b.createElement(a), 
       fs = b.getElementsByTagName(a)[0]; t.async=1; t.id=e; t.src=s; 
       fs.parentNode.insertBefore(t, fs); }) 
   (window,document,'script','https://scripts.demandbase.com/7d5ebf93.min.js','demandbase_js_lib');
   
   
   if(jQuery('.demandbase').length > 0){

window.dbAsyncInit = function () {
    var dbf = Demandbase.Connectors.WebForm;

    //map classes to form ID's for email and company
    var user_company_ID = jQuery('.demandbase .ginput_container input').attr('id'); //input company
    //var user_company_ID  = 'input_45_13';
    var user_email_ID = jQuery('.user_email').attr('id'); //input email

    //DemandBase fields
    var db_id_ID = jQuery('.db_id').attr('id');
    var company_ID = jQuery('.db_company').attr('id');
    var address_ID = jQuery('.db_address').attr('id');
    var industry_ID = jQuery('.db_industry').attr('id');
    var sub_industry_ID = jQuery('.db_sub_industry').attr('id');
    var annual_sales_ID = jQuery('.db_annual_sales').attr('id');
    var employee_range_ID = jQuery('.db_employee_range').attr('id');
    var revenue_range_ID = jQuery('.db_revenue_range').attr('id');
    var city_ID = jQuery('.db_city').attr('ID');
    var state_ID = jQuery('.db_state').attr('ID');
    var postal_code_ID = jQuery('.db_postal_code').attr('ID');
    var country_ID = jQuery('.db_country').attr('ID');
    var country_name = jQuery('.db_country_name').attr('ID');
    var phone_ID = jQuery('.db_phone').attr('ID');
    var latitude_ID = jQuery('.db_lat').attr('ID');
    var longitude_ID = jQuery('.db_long').attr('ID');
    var data_source_ID = jQuery('.db_data_source').attr('ID');
    var watch_list_ID = jQuery('.db_watch_list').attr('ID');

    var b2b_ID = jQuery('.db_b2b').attr('ID');
    var b2c_ID = jQuery('.db_b2c').attr('ID');
    var fortune1k_ID = jQuery('.db_fortune1k').attr('ID');
    var forbes2k_ID = jQuery('.db_forbes2k').attr('ID');
    var stock_ticker_ID = jQuery('.db_stock_ticker').attr('ID');
    var web_site_ID = jQuery('.db_web_site').attr('ID');
    var ip_ID = jQuery('.db_ip').attr('ID');
    var dma_code_ID = jQuery('.db_dma_code').attr('ID');
    var area_code_ID = jQuery('.db_area_code').attr('ID');
    var employee_count_ID = jQuery('.db_employee_count').attr('ID');
    var primary_sic_ID = jQuery('.db_primary_sic').attr('ID');
    var primary_naics_ID = jQuery('.db_primary_naics').attr('ID');
    var traffic_ID = jQuery('.db_traffic').attr('ID');
    var marketing_alias_ID = jQuery('.db_marketing_alias').attr('ID');

    dbf.connect({
        emailID: user_email_ID,
        companyID: user_company_ID,
        key: '96b804f998aaf4409cf333efdb2a3b3c',
        fieldMap: { //takes ID's to map fields from demandbase to
            'demandbase_sid':db_id_ID,
            'company_name': company_ID,
            'industry': industry_ID,
            'sub_industry':sub_industry_ID,
            'annual_sales': annual_sales_ID,
            'employee_range': employee_range_ID,
            'revenue_range': revenue_range_ID,
            'street_address': address_ID,
            'city': city_ID,
            'state': state_ID,
            'zip': postal_code_ID,
            'country': country_ID,
            'country_name': country_name,
            'phone': phone_ID,
            'latitude':latitude_ID,
            'longitude':longitude_ID,
            'data_source':data_source_ID,
            'watch_list_account_status': watch_list_ID,
            'b2b':b2b_ID,
            'b2c':b2c_ID,
            'fortune_1000':fortune1k_ID,
            'forbes_2000':forbes2k_ID,
            'stock_ticker':stock_ticker_ID,
            'web_site':web_site_ID,
            'ip':ip_ID,
            'registry_dma_code':dma_code_ID,
            'registry_area_code':area_code_ID,
            'employee_count':employee_count_ID,
            'primary_sic':primary_sic_ID,
            'primary_naics':primary_naics_ID,
            'traffic':traffic_ID,
            'marketing_alias':marketing_alias_ID
        }
    });
};

(function () {
    var dbt = document.createElement('script');
    dbt.type = 'text/javascript';
    dbt.async = true;
    dbt.id = 'demandbase-form';
    dbt.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'scripts.demandbase.com/formWidget.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(dbt, s);
})();

}

}




</script>
<!-- end Simple Custom CSS and JS -->
