$(function(){

    function getStates()
    {
      $('.loader').show();
      $.ajax({
                type: 'GET',
                dataType:"json",
                url: SITE_URL+"/admin/get-states",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                        var html = "<form id='commission-form' data-type='state'>";
                             $.each(response.states,function(key,val){

                                 html+='<div> <span>'+val.state+'</span> <input type="number" step="0.01" min=0 max=99 name="state['+val.state+']" value="'+val.commission_percent+'" />%</div>'
                             });

                            html+="<button type='submit'>Save</button></form>";

                            $('#data-wrapper').html(html);
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
    }


    function getServiceCommissions()
    {
      $('.loader').show();
      $.ajax({
                type: 'GET',
                dataType:"json",
                url: SITE_URL+"/admin/get-service-commissions",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                        var html = "<form id='commission-form' data-type='service'>";
                             $.each(response.services,function(key,val){

                                 html+='<div> <span>'+val.name+'</span> <input type="number" step="0.01" min=0 max=99 name="service['+val.id+']" value="'+val.commission_percent+'" />%</div>'
                             });

                            html+="<button type='submit'>Save</button></form>";

                            $('#data-wrapper').html(html);
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
    }


    function getPremiumBeautyPros()
    {
      $('.loader').show();
      $.ajax({
                type: 'GET',
                dataType:"json",
                url: SITE_URL+"/admin/get-premium-beautypros",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {

                       var beautyProRowHtml="";
                       $.each(response.premiumBeauticians,function(key,val){
                            beautyProRowHtml += "<li data-beautician-id='"+val.id+"'>";

                            beautyProRowHtml += "<span class='premium-beautypro-businessname'>"+val.business_name+"</span>";
                            beautyProRowHtml += "<button class='set-global-percent'>Set Global</button>";
                            beautyProRowHtml += "<button class='set-service-percent'>Set Service Wise</button>";
                            beautyProRowHtml += "<button class='remove'>Remove</button></li>";

                            premiumBeautyPros.push(val.id);
                       });


                       $('#premium-beautypro-list').append(beautyProRowHtml);
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
    }
   
   $('#state-percent-btn').on('click',function(){
          getStates(); 
   });


   $('#service-percent-btn').on('click',function(){
          getServiceCommissions(); 
   });

   $('#global-percent-btn').on('click',function(){

         getGlobalPercent();
   });


   function getGlobalPercent(){
    $('.loader').show();
    $.ajax({
                type: 'GET',
                dataType:"json",
                url: SITE_URL+"/admin/get-beautypro-global-percent",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                       var html = "<form id='commission-form' data-type='global'>";

                       html+='<div> <span>Set Global Percentage </span> <input type="number" step="0.01" min=0 max=99 name="global_percent" value="'+response.percent+'" />%</div>'
                                          
                        html+="<button type='submit'>Save</button></form>";

                       $('#data-wrapper').html(html); 
                    } 
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        });
   }

   $(document).on('click','#premium-beautypro-percent',function(){

        $('.loader').show();
        var html = "<select id='beautypro-selectbox' name='standard' class='custom-select'></select>"+

                     "<button id='add-premium-beauty-pro' class='btn btn-default'><span class='icon icon-add'></span> Add Beauty Pro</button>"+

                   "<ol id='premium-beautypro-list'></ol>";

        $('#data-wrapper').html(html);
        $.ajax({
                type: 'GET',
                dataType:"json",
                url: SITE_URL+"/admin/get-beautypro-listing",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                       var selectboxHtml = "<option value=''>None - Please Select</option>";
                                              
                                              $.each(response.beauticians,function(key,val){
                                                selectboxHtml+="<option value='"+val.id+"'>"+val.business_name+"</option>";
                                              });

                       $("#beautypro-selectbox").html(selectboxHtml);
                       $("#beautypro-selectbox").customselect();
                    } 
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        });

        getPremiumBeautyPros();
   });


   var premiumBeautyPros = [];
   $(document).on('click','#add-premium-beauty-pro',function(){
       var selectedVal = parseInt($("#beautypro-selectbox").val());
       var selectedText = $("#beautypro-selectbox option:selected").text();
       
       if(isNaN(selectedVal))
       {
        alert("Please select a beauty pro");
        return false;
       }

       if($.inArray(selectedVal,premiumBeautyPros) != -1)
       {
        alert("This beauty pro has already been added, please select a different one");
        return false;
       }

       
       var beautyProRowHtml = "<li data-beautician-id='"+selectedVal+"'>";

                    beautyProRowHtml += "<span class='premium-beautypro-businessname'>"+selectedText+"</span>";
                    beautyProRowHtml += "<button class='set-global-percent'>Set Global</button>";
                    beautyProRowHtml += "<button class='set-service-percent'>Set Service Wise</button>";
                    beautyProRowHtml += "<button class='remove unsaved'>Remove</button>";

                              "</li>";

       premiumBeautyPros.push(selectedVal);

       $('#premium-beautypro-list').append(beautyProRowHtml);


   });  

   $(document).on('click','.set-global-percent',function(){
    $('#global-percent-button-modal').modal('show');
    getBeauticianServiceCommissions($(this).closest('li').data('beautician-id'),true);
   });

   $(document).on('click','.set-service-percent',function(){
    $('#service-wise-percent-button-modal').modal('show');
    getBeauticianServiceCommissions($(this).closest('li').data('beautician-id'));
   });

   $('#service-wise-percent-button-modal form').on('submit',function(){
      var form = $(this);
      var serializedData = form.serialize();
      savePremiumServiceCommissions(serializedData);
      $('#service-wise-percent-button-modal').modal('hide');
      return false;

   });

   $(document).on('click','#premium-beautypro-list .remove',function(){
      var $this = $(this);
      var beauticianId = parseInt($this.closest('li').data('beauticianId'));
      if($this.hasClass('unsaved'))
      {
        $this.closest('li').remove();
        var index = premiumBeautyPros.indexOf(beauticianId);
        premiumBeautyPros.splice(index,1);

      }
      else
      {
        $('.loader').show();
        $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/remove-premium-commissions",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:{"beauticianId":beauticianId},
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                      $this.closest('li').remove();
                      var index = premiumBeautyPros.indexOf(beauticianId);
                      premiumBeautyPros.splice(index,1);
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
      }
    
   });

   $('#global-percent-button-modal form').on('submit',function(){
      var form = $(this);
      var serializedData = form.serialize();
      savePremiumGlobalCommission(serializedData);
      $('#global-percent-button-modal').modal('hide');
      return false;

   });

   $(document).on('submit','#commission-form',function(e){
        var form  = $(this);
        var type = form.data('type');
        var serializedData = form.serialize();
        if(type == 'state')
        {
           saveStateCommissions(serializedData);
        }
        else if(type == 'service')
        {
           saveServiceCommissions(serializedData);
        }
         else if(type == 'global')
        {
           saveGlobalCommissions(serializedData);
        }

        
        return false;

   });

   function saveStateCommissions(formData)
   {
     $('.loader').show();
     $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/save-state-commissions",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:formData,
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                      
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
   }


 function saveServiceCommissions(formData)
   {
     $('.loader').show();
     $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/save-service-commissions",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:formData,
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                      
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
   }


   function saveGlobalCommissions(formData)
   {
     $('.loader').show();
     $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/set-beautypro-global-commissions",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:formData,
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                      
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
   }


   function savePremiumGlobalCommission(formData)
   {
     $('.loader').show();
     $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/set-beautypro-premium-global-commissions",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:formData,
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                      
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
   }

   

    function savePremiumServiceCommissions(formData)
   {
     $('.loader').show();
     $.ajax({
                type: 'POST',
                dataType:"json",
                url: SITE_URL+"/admin/save-premium-service-commissions",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data:formData,
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                      
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
   }


    function getBeauticianServiceCommissions(beauticianId,isGlobal)
    {
      $('.loader').show();
      $.ajax({
                type: 'GET',
                dataType:"json",
                url: SITE_URL+"/admin/get-beautician-service-commissions?beauticianId="+beauticianId,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    $('.loader').hide();
                    if(response.success == true)
                    {
                           if(typeof isGlobal == "undefined")
                           {
                             var html = "";
                             $.each(response.services,function(key,val){

                                 var commissionPercent = val.premium_commission_percent;
                                 
                                 html+='<div> <span>'+val.name+'</span> <input type="number" step="0.01" min=0 max=99 name="beautician_service['+val.beautician_id+']['+val.service_id+']" value="'+commissionPercent+'" />%</div>'
                             });

                          

                            $('#service-wise-percent-button-modal .modal-body').html(html);
                           }
                           else
                           {
                            var globalVal = 0;
                            var currentVal=response.services[0].premium_commission_percent;
                            $.each(response.services,function(key,val){
                                if(val.premium_commission_percent != currentVal)
                                {
                                  globalVal = 0;
                                  return false;
                                }
                                else
                                {
                                    currentVal = val.premium_commission_percent;
                                    globalVal = currentVal;
                                }
                             });

                           

                             var html="<form><span>Global Percent</span> <input type='hidden' name='beauticianId' value='"+beauticianId+"'/><input type='number' step='0.01' min=0 max=99 name='commissionPercent' value='"+globalVal+"'/>%</form>";
                             $('#global-percent-button-modal .modal-body').html(html);
                           }


                           
                    }
                    
                },
                error: function (response) {
                     $('.loader').hide();
                    var response = JSON.parse(response.responseText);
                    $('.ajax-loader').hide();
                    alert(response.message);
                    
                }
        })
    }


});