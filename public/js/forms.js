$(document).ready(function(){
    $('#part').attr('type') == 'text' ? $('#part').mask('AAA/AAA') : '';
    $('#phone').mask('(00) 00000-0000 / (00) 00000-0000 / (00) 00000-0000');
    $('#cpf').mask('000.000.000-00');
    $('#cns').mask('000 0000 0000 0000');
    $('#rg').mask('000000000000000');
    $('#going_request').mask('0000');
    $('#return_request').mask('0000');
});

function Social_Name(){
    if(document.getElementById('use_sn').checked){
        document.getElementById('social_name').removeAttribute("disabled");
    }
    else{
        document.getElementById('social_name').setAttribute("disabled", "disabled");
    }
}

$("#name").on("input", function() {
    var regexp = /[^a-zA-Zà-úÀ-Ú '-.]/g;
    if(this.value.match(regexp)){
        $(this).val(this.value.replace(regexp,''));
    }
});

$("#page").on("input", function() {
    var regexp = /[^a-z-]/g;
    if(this.value.match(regexp)){
        $(this).val(this.value.replace(regexp,''));
    }
});
