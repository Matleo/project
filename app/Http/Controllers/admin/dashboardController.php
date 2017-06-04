<?php
/**
 * Created by PhpStorm.
 * User: Matthias
 * Date: 06.05.2017
 * Time: 17:44
 */

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class dashboardController
{

    public function showDashboard(Request $request)
    {
        $action = "noAction";
        if ($request->has('action')) {
            $action = $request->action;//nachdem löschen von Ratings oder dem Beenden der Wahl wird das entsprechende Modal  in der View geöffnet
        }
        $numberStudents = DB::table("users")->where("userlevel", 0)->count();
        $ratings = DB::table("ratings")->select("user")->distinct()->get();
        $numberRatings = sizeof($ratings);

        $noRating = $numberStudents - $numberRatings;

        $zugewieseneStudenten = DB::table("users")->whereNotNull("zugewiesen")->count();
        if($zugewieseneStudenten==0){
            $rated = false; //ob den Studenten bereits eine AG zugewiesen wurde
        }else{
            $rated = true;
        }
        $opened = DB::table("options")->select("opened")->get()[0]->opened;
        if($opened){
            $status = "open";
        }else{
            $status = "closed";
        }

        $parameter = ["numberStudents" => $numberStudents, "noRating" => $noRating, "rated" => $rated, "status" => $status, "action" => $action];

        return view("admin_dashboard", $parameter);
    }

    public function checkAdmin(Request $request)
    {
        $passwort = $request->param;
        $hashedPasswordObject = DB::table("users")->select("password")->where("userlevel", 1)->first();
        $hashedPassword = $hashedPasswordObject->password;

        //die Hash::check Funktion nutzt gleichen Hash Algorithmus wie Laravel Auth
        $matches = Hash::check($passwort, $hashedPassword);
        if ($matches) {
            DB::table("ratings")->delete();
            DB::table("users")->delete();
        }

        //var_export konvertiert den boolean in einen String
        return var_export($matches, true);;
    }

    public function deleteRatings(Request $request)
    {
        $passwort = $request->param;
        $hashedPasswordObject = DB::table("users")->select("password")->where("userlevel", 1)->first();
        $hashedPassword = $hashedPasswordObject->password;

        //die Hash::check Funktion nutzt gleichen Hash Algorithmus wie Laravel Auth
        $matches = Hash::check($passwort, $hashedPassword);
        if ($matches) {
            DB::table("ratings")->delete();
            DB::table("users")->where("userlevel", 0)->update(["zugewiesen" => NULL]);

        }

        return var_export($matches, true);;
    }

    public function deleteAssignments(Request $request)
    {
        $passwort = $request->param;
        $hashedPasswordObject = DB::table("users")->select("password")->where("userlevel", 1)->first();
        $hashedPassword = $hashedPasswordObject->password;

        //die Hash::check Funktion nutzt gleichen Hash Algorithmus wie Laravel Auth
        $matches = Hash::check($passwort, $hashedPassword);
        if ($matches) {
            DB::table("users")->where("userlevel", 0)->update(["zugewiesen" => NULL]);
        }
        return var_export($matches, true);;
    }

    public function startAlgo()
    {
        $students = DB::table("users")->where("userlevel",0)->get();
        $anzahlStudents = sizeof($students);
        //für alle Studenten ein Attribut "priorität" mit 0 setzen
        foreach ($students as $student) {
            $student->priorität = 0;
        }

        //$i ist Laufvariable für den Wert eines rating
        for ($i = 10; $i >= 1; $i--) {
            //Alle Bewertungen mit Präferenz $i raussuchen
            $präferenzRatings = array();//Array voll mit, userid=>workgroupid, wobei der user die workgroup mit $i bewertet hat
            foreach ($students as $student) {
                $ratings = DB::table("ratings")->where("user", $student->id)->get();
                foreach ($ratings as $rating) {
                    if ($rating->rating == $i) {
                        $präferenzRatings[$student->id] = $rating->workgroup;
                    }
                }
            }
            Log::info("PräferenzRatings: ".print_r($präferenzRatings,true). "\n");

            //Nach AGs sortieren
            $workgroups = array_unique(array_values($präferenzRatings));//Array aller AGs, jede AG nur einmal vorhanden
            foreach ($workgroups as $workgroup) {
                //Alle Studenten, welche diese AG mit $i bewertet haben
                $ratedStudents = array_keys($präferenzRatings, $workgroup);//gibt alle schlüssel die den wert von $workgroup haben zurück

                $plätzeObject = DB::table("workgroups")->select("spots")->where("id", $workgroup)->get();
                $plätze = $plätzeObject[0]->spots;
                //Falls Anzahl der Bewertungen <= Plätze-> alle zuweisen
                if (sizeof($ratedStudents) <= $plätze) {
                    foreach ($ratedStudents as $ratedStudent) {
                        //Spalte in DB aktualisieren
                        DB::table("users")->where("id", "$ratedStudent")->update(["zugewiesen" => $workgroup]);
                        Log::info('Dem Studenten: ' . $ratedStudent ." wurde die Gruppe ".$workgroup." zugewiesen. Bewertung=".$i. "\n");
                        //und den studenten aus Algorithmus entfernen
                        $index = -1; //der index des studenten in $students
                        for ($j = 0; $j < $anzahlStudents; $j++) {
                            Log::info('Neue Iteration:' . $j . "\n");
                            if (isset($students[$j])) {
                                if ($students[$j]->id == $ratedStudent) {
                                    $index = $j;
                                    Log::info('Found the student:' . $ratedStudent . "\n");
                                    break;
                                }
                            }
                        }
                        $students->forget($index);
                        Log::info('Forgot the student:' . $ratedStudent .". Es sind noch ".sizeof($students). " zu verteilen \n");
                        Log::info(print_r($students,true). "\n");
                    }
                } //ansonsten Studenten mit höchster Priorität zuweisen. Danach AG mit zufälligen Studenten auffüllen
                else {
                    //alle Objecte von den passenden Studenten
                    return ("Du bist im Else!");
                    $ratedStudentsObject = array();
                    foreach ($students as $student) {
                        foreach ($ratedStudents as $ratedStudent) {
                            //passenden studenten gefunden
                            if ($student->id == $ratedStudent) {
                                array_push($ratedStudentsObject, $student);
                            }
                        }
                    }

                    $maxPrio = max(array_column($ratedStudentsObject, "priorität"));
                    //solange Studenten der höchsten Priorität zuweisen, bis voll
                    while ($plätze > 0) {
                        foreach ($ratedStudentsObject as $ratedStudentObject) {
                            if ($ratedStudentObject->priorität == $maxPrio) {
                                //Spalte in DB aktualisieren
                                DB::table("users")->where("id", $ratedStudentObject->id)->update(["zugewiesen"=> $workgroup]);
                                //und den studenten aus Algorithmus entfernen
                                $index = -1; //der index des studenten in $students
                                foreach ($students as $student) {
                                    if ($student->id == $ratedStudentObject->id) {
                                        $index = key($student);
                                    }
                                }
                                $students->forget($index);
                                $ratedStudentsObject->forget(key($ratedStudentObject));
                                $maxPrio = max(array_column($ratedStudentsObject, "priorität"));
                                $plätze--;
                            }
                        }
                    }
                    //Studenten, welche nicht zugewiesen wurden, das Attribut Priorität um 1 erhöhen
                    if (sizeof($ratedStudentsObject) > 0) {
                        foreach ($ratedStudentsObject as $ratedStudentObject) {
                            foreach ($students as $student) {
                                if ($ratedStudentObject->id == $student->id) {
                                    $student->priorität++;
                                }
                            }
                        }
                    }
                }

            }
            //Wenn alle studenten zugewiesen worden, kann abgebrochen werden.
            if(sizeof($students)==0){
                break;
            }
        }
        return "true";
    }

    public function toggleOpened1(){
        $opened = DB::table("options")->select("opened")->get()[0]->opened;
        if($opened==1){
            DB::table("options")->update(["opened"=>0]);
            return view("ajax.admin_statusfield",["status"=>"close"]);
        }else{
            DB::table("options")->update(["opened"=>1]);
            return view("ajax.admin_statusfield",["status"=>"open"]);
        }
    }
    public function toggleOpened2(){
        //opened wurde gerade durch toggleOpened1() verändert
        $opened = DB::table("options")->select("opened")->get()[0]->opened;
        if($opened==1){
            return view("ajax.admin_closeOpenButton",["status"=>"open"]);
        }else{
            return view("ajax.admin_closeOpenButton",["status"=>"close"]);
        }
    }
}
