
    



 $(document).ready(function () {
  $(function () {

    var dateFormat = "yy/mm/dd";
 

var startdate = $('#startdate');
var enddate = $('#enddate');

$.timepicker.datetimeRange(
	startdate,
	enddate,
	{
		minInterval: (1000*60*60), // 1hr
		dateFormat:dateFormat, 
		timeFormat: 'HH:mm',
                maxDate: 0,
                controlType: 'select',
	        oneLine: true,

		start: {}, // start picker options
		end: {} // end picker options					

	}
);




});
 $("#export-feedback-details").click(function(e) {
     
      var startdate = $('#startdate').val();
      var enddate = $('#enddate').val();
      var url=SITE_URL + '/admin/export-report/export-feedback-details';
      e.preventDefault();
    
      if(startdate != '' && enddate != '')
      {
            sendAjaxcall(url,startdate,enddate);  
      }else{
          
          alert("Please fill date range .")
      }
    

     
 }); 
  
});




function sendAjaxcall(url,startdate,enddate) {
    
   var timezone = new Date(startdate).toString().match(/([A-Z]+[\+-][0-9]+)/)[1];//Y-m-d H:i:s
   var startdate =new Date(startdate).toUTCString();
   var enddate =new Date(enddate).toUTCString();
  location.href = url+'?start_date='+startdate+'&end_date='+enddate+'&time_zone='+encodeURIComponent(timezone); 
 
}
