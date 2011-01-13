$().ready(function(){
    $('form.confirm-delete').live('submit',function(){
        var file = $(this).find('input[name=file]').val();
        return confirm('Are you sure you want to delete ' + file + '?');
    });
    $('.qq-upload-fail, .qq-upload-success').live('dblclick', function(){
        $(this).fadeOut();
    });
    $('input.read-only').live('click',function(){
        $(this).select();
    });
    //add the uploader to the interface if needed
    (function(){
        var element = document.getElementById('file-uploader');
        if(element){
            new qq.FileUploader({
                element: element,
                action: base_url + 'uploadr/upload',
                onComplete: function(id, fileName, responseJSON){
                    if(responseJSON.success == false){
                        var $list = $('.qq-upload-list');
                        //add the error message to the element
                        $list.find('li:nth-child('+(id + 1)+')').append('<span class="error small">'+responseJSON.error+'</span>');
                    } else {
                        //replace the current file list with the new one
                        $('.files-scroll').load(base_url+' #files')
                    }
                }
            });
        }
    })();
});