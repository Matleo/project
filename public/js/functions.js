function anhaengen(){
    $('#AG_table tr:last').after('<tr><td style="display:none"><input name="id[]" class="id" form="AG_form"></td><td><input name ="groupLeader[]" class="gl form-control" form="AG_form"></td><td><input name ="name[]" class="gn form-control" form="AG_form"></td><td><input name="spots[]" class="pl form-control" type="number" form="AG_form"></td><td><input name="date[]" class="zp form-control" form="AG_form"></td><td><button type="button" class="löschButton btn btn-default btn-xs form-control" data-toggle="modal" data-target="#löschModal"><span class="icon icon-minus"></span></button></td></tr>');

    $('.löschButton').click(function () {
        trigger=this;
    });
}
function update(){
    $('#AG_anz').html($("#AG_table tr").length -1);
}

var trigger;

//wird aufgerufen, nach dem Bestätigen vom Löschen einer AG
function deleteTrigger(){
    var row = $(trigger).parent().parent();
    var ID = row.find("input.id").val();
    //wenn eine AG aus DB ist, dann ist ID definiert
    if(ID !== ""){
        $("#AG_table").load("admin_AG_delete?id="+ID , function(){
            update();
            $('.löschButton').click(function () {
                trigger = this;
            });
        });
        //ansonsten wurde die AG gerade erst eingegeben, und wird jetzt doch gelöscht
    }else{
        $(row).remove();
    }
}
//Für /admin_AG: Wenn keine Gruppenleiter/name oder Plätze einer AG leer ist, wird der aktuelle AG stand in die Datenbank geschrieben. Ansonsten wird der User darauf hingewiesen.
function checkSave(){
    var valide = true;
    //über alle Gruppennamen
    $("input.gl").each(function() {
        if($(this).val()==""){
            valide = false;
        }
    });
    $("input.gn").each(function() {
        if($(this).val()==""){
            valide = false;
        }
    });
    $("input.pl").each(function() {
        if($(this).val()==""){
            valide = false;
        }
    });
    if(valide==true){
        $('#ag_alert').hide();
        //sobald modal geschlossen wird, wird form abgeschickt
        $("#save_ag_button").click(function(){
            $("#AG_form").submit();
        });
        $('#speicherModal').modal('toggle');

    }else{
        $('#ag_alert').show();
    }
}




//delStudenten-Modal lösch Button wird hier ausgeführt -> Student wird aus DB gelöscht
var triggerStudent;
function deleteStudentTrigger(){
    //Get anfrage an /delstudent
    var row = $(triggerStudent).parent().parent().parent();
    var matrnr = $(row).find('.ma').html();
    var name = $(row).find('.na').html();
    window.location = "/studenten_delete?"+"matrnr="+matrnr+"&name="+name;

}

$(document).ready(function() {

    //$('table .btn-group').parent().width($('table .btn-group').width());

    $('.löschButton').click(function () {
      trigger = this;
    });

    //modal für das löschen einer AG
    $('#löschModal').on('show.bs.modal', function () {
        var row = $(trigger).parent().parent();
        var ID = row.find("input.id").val();
        var GL = row.find("input.gl").val();
        var GN = row.find("input.gn").val();
        var PL = row.find("input.pl").val();
        var ZP = row.find("input.zp").val();

        $('#insert-ag .id').html(ID);
        $('#insert-ag .gl').html(GL);
        $('#insert-ag .gn').html(GN);
        $('#insert-ag .pl').html(PL);
        $('#insert-ag .zp').html(ZP);
    });


    $('.löschStudentButton').click(function () {
        triggerStudent = this;
    });
    $('#löschStudentModal').on('show.bs.modal', function () {
        var row = $(triggerStudent).parent().parent().parent();
        var Ma = row.find("td.ma").html();
        var NA = row.find("td.na").html();
        var ZA = row.find("td.za").html();

        $('#insert-student .ma').html(Ma);
        $('#insert-student .na').html(NA);
        $('#insert-student .za').html(ZA);
    });

    //Get Request an _bearbeiten mit Daten des zu bearbeitenden Studenten
    $('.bearbeitenButton').click(function(){
        var row = $(this).parent().parent().parent();
        var id = $(row).find('.id').html();
        window.location = "/admin_studenten_bearbeiten?"+"id="+id;
    });





    //Dashboard-buttons:
    $('#wahl_schliessen').click(function(){
        if($('#wahl_schliessen_text').html() == ' Wahl schließen'){
            $('#close_open').html('geschlossen');
            $('#close_open_body').html('<ul><li>Es können sich keine neuen Studenten anmelden</li><li>Die Studenten können ihre getroffene Wahl und ihr Profil nicht mehr selbstständig ändern</li><li>Dies können nur noch sie als Administrator unter dem Reiter "Übersicht Studenten"</li><li>Sollten sie die Wahl wieder eröffnen wollen, können sie dies über denselben Button tun, den sie zum schließen genutzt haben</li></ul>');
            $('#wahl_schliessen_text').html(' Wahl öffnen');
            $('#wahl_schliessen').removeClass("icon-block");
            $('#wahl_schliessen').addClass("icon-controller-play");
        }
        else if($('#wahl_schliessen_text').html() == ' Wahl öffnen'){
            $('#close_open').html('geöffnet');
            $('#close_open_body').html('<ul><li>Ab jetzt können sich neue Studenten anmelden</li><li>Alle angemeldeten Studenten können neue Wahlen treffen,diese verändern oder ihr Profil bearbeiten</li><li>Sollten sie die Wahl schließen wollen, können sie dies über denselben Button tun, den sie zum öffnen genutzt haben</li></ul>');
            $('#wahl_schliessen_text').html(' Wahl schließen');
            $('#wahl_schliessen').removeClass("icon-controller-play");
            $('#wahl_schliessen').addClass("icon-block");
        }

        if($('#wahl_schliessen_text').html() == ' Close the rating-process'){
            $('#close_open').html('Closed');
            $('#close_open_body').html('<ul><li>No more students can be registered</li><li>The students cannot change their profile data or ratings themselves</li><li>Only you as administrator can do so, under "Students overview"</li><li>If you want to open the rating-process, you can, by pressing the same button that you used to close</li></ul>');
            $('#wahl_schliessen_text').html(' Open the rating-process');
            $('#wahl_schliessen').removeClass("icon-block");
            $('#wahl_schliessen').addClass("icon-controller-play");
        }
        else if($('#wahl_schliessen_text').html() == ' Open the rating-process'){
            $('#close_open').html('Open');
            $('#close_open_body').html('<ul><li>From now on, new students can register</li><li>All registered students can change their profile and ratings</li><li>If you want to close the rating-process, you can, by pressing the same button that you used to open</li></ul>');
            $('#wahl_schliessen_text').html(' Close the rating-process');
            $('#wahl_schliessen').removeClass("icon-controller-play");
            $('#wahl_schliessen').addClass("icon-block");
        }
    });

    $('#start_Algo').click(function(){
        if (document.getElementById('start_Algo').classList.contains('disabled')) {
            $('#dashboard_alert').show();
        }
        else{
            //start algo
        }

    });
    $('#close_alert').click(function() {
        $('#dashboard_alert').hide()
    });
    $('#close_AG_alert').click(function() {
        $('#ag_alert').hide()
    });

});


