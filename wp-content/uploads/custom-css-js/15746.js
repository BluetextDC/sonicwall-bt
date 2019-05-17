<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
   (function(d,b,a,s,e){ var t = b.createElement(a), 
       fs = b.getElementsByTagName(a)[0]; t.async=1; t.id=e; t.src=s; 
       fs.parentNode.insertBefore(t, fs); }) 
   (window,document,'script','https://scripts.demandbase.com/7d5ebf93.min.js','demandbase_js_lib');
   
   
   if($('.demandbase').length > 0){

window.dbAsyncInit = function () {
    var dbf = Demandbase.Connectors.WebForm;

    //map classes to form ID's for email and company
    var user_company_ID = $('.user_company').attr('id'); //input company
    var user_email_ID = $('.user_email').attr('id'); //input email

    //DemandBase fields
    var db_id_ID = $('.db_id').attr('id');
    var company_ID = $('.db_company').attr('id');
    var address_ID = $('.db_address').attr('id');
    var industry_ID = $('.db_industry').attr('id');
    var sub_industry_ID = $('.db_sub_industry').attr('id');
    var annual_sales_ID = $('.db_annual_sales').attr('id');
    var employee_range_ID = $('.db_employee_range').attr('id');
    var revenue_range_ID = $('.db_revenue_range').attr('id');
    var city_ID = $('.db_city').attr('ID');
    var state_ID = $('.db_state').attr('ID');
    var postal_code_ID = $('.db_postal_code').attr('ID');
    var country_ID = $('.db_country').attr('ID');
    var country_name = $('.db_country_name').attr('ID');
    var phone_ID = $('.db_phone').attr('ID');
    var latitude_ID = $('.db_lat').attr('ID');
    var longitude_ID = $('.db_long').attr('ID');
    var data_source_ID = $('.db_data_source').attr('ID');
    var watch_list_ID = $('.db_watch_list').attr('ID');

    var b2b_ID = $('.db_b2b').attr('ID');
    var b2c_ID = $('.db_b2c').attr('ID');
    var fortune1k_ID = $('.db_fortune1k').attr('ID');
    var forbes2k_ID = $('.db_forbes2k').attr('ID');
    var stock_ticker_ID = $('.db_stock_ticker').attr('ID');
    var web_site_ID = $('.db_web_site').attr('ID');
    var ip_ID = $('.db_ip').attr('ID');
    var dma_code_ID = $('.db_dma_code').attr('ID');
    var area_code_ID = $('.db_area_code').attr('ID');
    var employee_count_ID = $('.db_employee_count').attr('ID');
    var primary_sic_ID = $('.db_primary_sic').attr('ID');
    var primary_naics_ID = $('.db_primary_naics').attr('ID');
    var traffic_ID = $('.db_traffic').attr('ID');
    var marketing_alias_ID = $('.db_marketing_alias').attr('ID');

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



</script>
<!-- end Simple Custom CSS and JS -->
