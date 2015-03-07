<?php

global $reportError;
global $reportMessage;
global $userId;
global $courseId;
global $report;
global $COMMENTS_REPORT_TITLE;
global $QUIZ_REPORT_TITLE;

$COMMENTS_REPORT_TITLE = 'comments';
$QUIZ_REPORT_TITLE = 'questionnaires';

if(!$reportError){
    
    $report = null;
    $assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor'];
    
    Dbase::Connect();

    $roleInCourse = Dbase::GetUserRoleInCourse($userId, $courseId);
    
    if( $roleInCourse == "in" || $assessor){
        $allStudents = Dbase::GetUsersFromCourse($courseId, "st");
        $ids = array();
        foreach ($allStudents as $st) {
            if($st['studentid'] != 0){
                array_push($ids, $st['studentid']);
            }
        }
        $course = Dbase::GetCourseInfo($courseId);
        $report = array(
            'title' => $course['title'] . " (" . Dbase::GetTermRef($course['term_code']) . " " . $course['year'] . ")",
        );

        $courseCommentNumber  = $studentCourseCommentNumber = $courseQuizzNumber =
                                $answerdCourseQuizzNumber = $totalQuizAnswer = $sessionAddressedCommentsCounter = 0;
                                
        $courseSessions = Dbase::GetSessions($courseId);
        $sessions = array();
        foreach($courseSessions as $sess){
            $sessionComments = Dbase::GetCommentsFromSession($sess['id']);
            $sessionAddressedCommentsCounter = $sessionHiddenCommentsCouner = 0;
            foreach($sessionComments as $com){
                $courseCommentNumber++;
                if(Dbase::GetUserRoleInCourse($com['user_id'], $courseId) != "in"){ //Only comments by student
                    $studentCourseCommentNumber++;
                }
                $com['flag_id'] == '3' ? $sessionAddressedCommentsCounter++ : $com['flag_id'] == '4' ? $sessionHiddenCommentsCouner++ : "" ;
            }
            $sessionQuizzes = Dbase::GetQuizzes($sess['id']);
            $courseQuizzNumber += count($sessionQuizzes);
             $session_answers_counter = $session_correct_answers_counter = $session_wrong_answers_counter = 0;
            foreach ($sessionQuizzes as $quz){
                $answer = 0;
                $i = 0;
                foreach($quz['choices'] as $key => $val){
                    if($key != "quiz"){
                        $answer+=$val;
                        $totalQuizAnswer+=$val;
                        $session_answers_counter+=$val;
                        if($quz['form']['answer'] !== "0"){//quiz has a correct answer
                            ($i."" == $quz['form']['answer']) ? $session_correct_answers_counter+=$val : $session_wrong_answers_counter+=$val;
                        }
                    }
                $i++;
                }
                if($answer>0){
                    $answerdCourseQuizzNumber++;
                }
            }

            array_push($sessions, array(  
                                        'date' => date("d M", $sess['date']), 
                                        'comments' => count($sessionComments), 
                                        'hidden_comments' => $sessionHiddenCommentsCouner, 
                                        'addressed_comments' => $sessionAddressedCommentsCounter, 
                                        'participant' => round($session_answers_counter/count($sessionQuizzes),2), 
                                        'correct_answres' => round($session_correct_answers_counter/count($sessionQuizzes),2), 
                                        'wrong_answres' => round($session_wrong_answers_counter/count($sessionQuizzes),2)
                                        )
                    );

        }
        $report['reports']['students'] = array(
            'registered_students' => count($ids)>0 ? implode(", ", $ids) : "No Registered Ids"
        );
        $report['reports'][$COMMENTS_REPORT_TITLE] = array(
            'number_of_comments'       =>$courseCommentNumber ,
            'number_of_comments_by_students'=>$studentCourseCommentNumber ,
            'average_students_comments_per_session'      =>round($studentCourseCommentNumber/count($courseSessions),2)
        );
        $report['reports'][$QUIZ_REPORT_TITLE] = array(
            'number_of_questionnaires'       =>$courseQuizzNumber ,
            'number_of_answered_questionnaires'=>$answerdCourseQuizzNumber ,
            'number_of_participants'      =>$totalQuizAnswer,
            'average_participation_per_questionnaire'      =>round($totalQuizAnswer/$courseQuizzNumber,2),
            'average_answered_questionnaires_per_session'      =>round($answerdCourseQuizzNumber/count($courseSessions),2)
        );
        $report['reports']['sessions'] = array_reverse($sessions);
    }else {
        $reportError = true;
        $reportMessage = "You do not have access to this course!";
    }

    Dbase::Disconnect();
}
