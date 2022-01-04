var count=0;
function select2object(ajaxtype){
    return {
     minimumInputLength: 3,   
      ajax: {
        url: ajaxurl,
        dataType: 'json',
        data: function (params) {
          var query = {
            action: ajaxtype+'_ajax',
            search: params.term
          }

          // Query parameters will be ?search=[term]&page=[page]
          return query;
        }

      }
    };
}
jQuery(document).ready(function() {
    var i=0;
    fileupload();
    delete_media();
   jQuery('#wcpoa-ui-tbody tr:not(:last) .productlist').select2(select2object('product'));
    jQuery('#wcpoa-ui-tbody tr:not(:last) .catlist').select2(select2object('category'));
    jQuery('#wcpoa-ui-tbody tr:not(:last) .taglist').select2(select2object('tag'));
    customValidation();
    jQuery('body').on('click', '.wcpoa-button,.wcpoa-icon.-plus', function (e) {
        e.preventDefault();
        var trr= jQuery('.trr');
        createAttachment(trr);
        count++;
        i++;
    })
    jQuery('.wcpoa_att_download_btn').click(function(){
        var buttontype=jQuery(this).val();
        if(buttontype=='wcpoa_att_btn'){
            jQuery('.wcpoa_att_icon_file_selected').addClass('hide');
        } else{
           jQuery('.wcpoa_att_icon_file_selected').removeClass('hide'); 
        }
    })
    jQuery('body').on('change','.is_condition_select',function(){
        if(jQuery(this).val()=='yes'){
            jQuery(this).parent().parent().parent().find('.is_condition').removeClass('hide');
        } else{
            jQuery(this).parent().parent().parent().find('.is_condition').addClass('hide');
        }
    });
    jQuery('body').on('focus',".wcpoa-php-date-picker", function(){
        jQuery(this).datepicker({ dateFormat: 'yy/mm/dd', minDate : 0 });
    });
    jQuery('body').on('change','.enable_date_time',function(){
        var att=jQuery(this).parent().parent().parent();
        if(jQuery(this).val()=='yes'){
            jQuery(att).find('.enable_time').hide();    
            jQuery(att).find('.enable_date').show();
        } else if(jQuery(this).val()=='time_amount'){
            jQuery(att).find('.enable_time').show();    
            jQuery(att).find('.enable_date').hide();
        } else{
            jQuery(att).find('.enable_time').hide();    
            jQuery(att).find('.enable_date').hide();
        }
    });
    jQuery('body').on('change','.wcpoa_attach_type_list',function(){
        var att=jQuery(this).parent().parent().parent(); 
        var type=jQuery(this).val(); 
        jQuery(att).find('.file_upload,.external_ulr').hide();    
        jQuery(att).find('.'+type).show();
    })          
    jQuery('body').on('click','.-minus',function(){
        var element=jQuery(this).parent().parent();
        delete_row(element);
    })
    jQuery('body').on('click', '.group-title,.wcpoa-icon.-collapse', function(e){
        e.preventDefault();
        jQuery(this).parent().parent().toggleClass('-collapsed');
    })
});
function customValidation(){
    jQuery("#post").validate({ 
        rules: {        
        },
        submitHandler:function(form) {
            var isValid = true;

            jQuery("input.wcpoa-attachment-name").each(function() {
                if(!jQuery(this).parent().parent().parent().parent().hasClass('hidden')){
                    if(jQuery(this).val() == "" && jQuery(this).val().length < 1) {
                        jQuery(this).addClass('error');
                        var ediv=jQuery(this).parent().get(0).appendChild(document.createElement('p'));
                    
                        isValid = false;
                    } else {
                       jQuery(this).removeClass('error');
                    }

                }
            });  
            jQuery('.wcpoa_attach_type_list').each(function(){
                if(!jQuery(this).parent().parent().parent().parent().hasClass('hidden')){
                    if(jQuery(this).val()=='file_upload'){
                         if(jQuery(this).parent().parent().parent().find('.wcpoa-file-uploader input').val()==''){
                                 isValid = false;
                                 jQuery(this).parent().parent().parent().find('.wcpoa-file-uploader input').addClass('error');
                                 jQuery(this).parent().parent().parent().find('.wcpoa-file-uploader .wcpoa-error-message').show();
                         }  else{
                                 jQuery(this).parent().parent().parent().find('.wcpoa-file-uploader .wcpoa-error-message').hide();                                jQuery().hide();
                         } 
                    } else{
                        if(jQuery(this).parent().parent().parent().find('.wcpoa-attachment-url').val()==''){
                                 isValid = false;
                                 jQuery(this).parent().parent().parent().find('.wcpoa-attachment-url').addClass('error');
                         }  else{
                                jQuery(this).parent().parent().parent().find('.wcpoa-attachment-url').removeClass('error');
                         }     
                    }
                    
                }
            })

            jQuery('.enable_date_time').each(function(){
                if(!jQuery(this).parent().parent().parent().parent().hasClass('hidden')){
                    if(jQuery(this).val()=='yes'){
                         if(jQuery(this).parent().parent().parent().find('input.wcpoa-php-date-picker').val()==''){
                                 isValid = false;
                                 jQuery(this).parent().parent().parent().find('input.wcpoa-php-date-picker').addClass('error');
                            }  else{
                                 jQuery(this).parent().parent().parent().find('input.wcpoa-php-date-picker').removeClass('error');                              
                         } 
                    } else if(jQuery(this).val()=='time_amount'){
                        if(jQuery(this).parent().parent().parent().find('input.wcpoa-attachment-_time-amount').val()==''){
                                 isValid = false;
                                 jQuery(this).parent().parent().parent().find('input.wcpoa-attachment-_time-amount').addClass('error');
                         }  else{
                                jQuery(this).parent().parent().parent().find('input.wcpoa-attachment-_time-amount').removeClass('error');
                         }     
                    }
                    
                }
            })


            if(isValid) {
                form.submit();
            } else{
                alert("Please fill required fields");
            }          
        }
    }); 
}

function createtag(element,tag,attributes){
    var tag=document.createElement(tag);
    setAllAttributes(tag,attributes);
    element.appendChild(tag);
    return document.getElementById(attributes['id']);    
}

function responseTable(element,response){
var table=createtag(element,'table',{'id': 'datatable'});
var thead=createtag(table,'thead',{'id': 'datahead'});
var headtitles=["Page/Post Id","Page/Post Name","Page/Post Status", "URL"];
createCustomRow(thead,'th',headtitles,{'id':'datath'});
var tbody=createtag(table,'tbody',{'id': 'databody'});
for(var i=0; i<response.length;i++){
    data=Object.values(response[i]);
    createCustomRow(tbody,'td',data,{'id' : 'datatd-'+i});
}
}
function createCustomRow(element,celltype,data,attributes){
    data=Object.values(data);
    var tr=createtag(element,'tr',attributes);
    for(var i=0;i<data.length;i++){        
        var cell=createtag(tr,celltype,{"class": "wcpoa-fields -left",'id': attributes['id']+'-'+celltype+'-'+i});
        var text = document.createTextNode(data[i]);
        cell.appendChild(text); 
        tr.appendChild(cell);
    }
    return attributes['id'];
}
function setAllAttributes(element,attributes){
    Object.keys(attributes).forEach(function (key) {
        element.setAttribute(key, attributes[key]);
        // use val
    });
    return element;
}
function insertOptions(parentElement,options){
    for(var i=0;i<options.length;i++){
        if(options[i].type=='optgroup'){
            optgroup=document.createElement("optgroup");
            optgroup=setAllAttributes(optgroup,options[i].attributes);
            for(var j=0;j<options[i].options.length;j++){
                option=document.createElement("option");
                option=setAllAttributes(option,options[i].options[j].attributes);
                option.textContent=options[i].options[j].name
                optgroup.appendChild(option);
            }
            parentElement.appendChild(optgroup);
        } else {
            option=document.createElement("option");
            option=setAllAttributes(option,options[i].attributes);
            option.textContent=allowSpeicalCharacter(options[i].name);
            parentElement.appendChild(option);
        }

    }
    return parentElement;
    
}
function allowSpeicalCharacter(str){
            return str.replace('&#8211;','–').replace("&gt;",">").replace("&lt;","<").replace("&#197;","Å");    
}
function makeid(length) {
   var result           = '';
   var characters       = 'abcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

function createAttachment(element){
    var cln = element[0].cloneNode(true);
    // Append the cloned <li> element to <ul> with id="myList1"
    var tbody=document.getElementById('wcpoa-ui-tbody');
    tbody.appendChild(cln);
    var last_attachment_id=makeid(13);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2)').find('.wcpoa_attachments_id').val(last_attachment_id);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2)').removeClass('hidden');
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2)').removeClass('trr');
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2)').attr('id',last_attachment_id);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2)').attr('data-id',last_attachment_id);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .misha_upload_image_button').attr('data-id',last_attachment_id);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .misha_upload_image_button').attr('data-id',last_attachment_id);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .wcpoa-icon.-pencil').attr('data-id',last_attachment_id);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .wcpoa-icon.-cancel').attr('data-id',last_attachment_id);
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .productlist').attr('name','wcpoa_product_list['+last_attachment_id+'][]').select2(select2object('product'));
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .catlist').attr('name','wcpoa_category_list['+last_attachment_id+'][]').select2(select2object('category'));
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .taglist').attr('name','wcpoa_tag_list['+last_attachment_id+'][]').select2(select2object('tag'));
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .wcpoa_product_variation input').attr('name','wcpoa_variation['+last_attachment_id+'][]');
    jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .wcpoa-order-checkbox-list input').attr('name','wcpoa_order_status['+last_attachment_id+'][]');

    var lasttr=jQuery('#wcpoa-ui-tbody tr:nth-last-child(3) .order span').get(0);
    if(lasttr!=undefined){
        var index=parseInt(lasttr.innerHTML)+1;
        var ono=jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .order span').get(0);
        ono.innerHTML='';
        setText(ono,index);
        var ono=jQuery('#wcpoa-ui-tbody tr:nth-last-child(2) .group-title label').get(0);
        if(ono!=undefined){
            ono.innerHTML='';
            setText(ono,"Attachment: "+index);                
        }


    }
    customValidation();   
    return;      
}

function setText(element,text){
    var text = document.createTextNode(text);
    element.appendChild(text);        
}
function delete_row(element){
    var con=confirm("Are you sure you want to delete.");
    if(con){
        element.remove();
    }
}
function delete_media(){
    jQuery('body').on('click', '.wcpoa-icon.-cancel', function(e){
        e.preventDefault();
        element=jQuery("#"+jQuery(this).attr('data-id'));
        var con=confirm("Are you sure you want to delete.");
        if(con){
            element.find('.wcpoa-file-uploader input').val('');
            element.find('.wcpoa-file-uploader').removeClass('has-value');

        }
    });
}

function fileupload(){
    jQuery('body').on('click', '.misha_upload_image_button, .file-info .wcpoa-icon.-pencil', function(e){
        e.preventDefault();
            var attachment_div=jQuery('#'+jQuery(this).attr('data-id')).find('.wcpoa-file-uploader');
            
            var button = jQuery(this),
                custom_uploader = wp.media({
            title: 'Insert file',
            button: {
                text: 'Use this file' // button label text
            },
            multiple: false
        }).on('select', function() { // it also has "open" and "close" events 
            jQuery(attachment_div).addClass('has-value');
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery(attachment_div).find('.wcpoa-error-message input').val(attachment.id);
            jQuery(attachment_div).find('.file-info p:nth-child(1) strong').text(attachment.title);
            jQuery(attachment_div).find('.file-info a').text(attachment.filename);
            jQuery(attachment_div).find('.file-info span').text(attachment.size);
        })
        .open();
    });
    jQuery('body').on('click', '.misha_remove_image_button', function(){
        jQuery(this).hide().prev().val('').prev().addClass('button').html('Upload file');
        return false;
    });
}