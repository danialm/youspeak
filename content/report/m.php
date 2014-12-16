<?php

global $reportCourseId;
global $reportError;
global $reportMessage;
global $userId;
global $report;
global $courses;

if(!$reportError){
    Dbase::Connect();

    if($reportCourseId && $reportCourseId != ''){
        if(Dbase::GetUserRoleInCourse($userId, $reportCourseId) == "in"){
            $course = Dbase::GetCourseInfo($reportCourseId);
            $report = array(
                'title' => $course['title'] . " (" . Dbase::GetTermRef($course['term_code']) . " " . $course['year'] . ")",
            );

            $courseCommentNumber = 0;
            $studentCourseCommentNumber = 0;
            $courseQuizzNumber = 0;
            $answerdCourseQuizzNumber = 0;
            $totalQuizAnswer = 0;
            $courseSessions = Dbase::GetSessions($reportCourseId);
            foreach($courseSessions as $sess){
                $sessionComments = Dbase::GetCommentsFromSession($sess['id']);
                foreach($sessionComments as $com){
                    $courseCommentNumber++;
                    if(Dbase::GetUserRoleInCourse($com['user_id'], $reportCourseId) != "in"){ //Only comments by student
                        $studentCourseCommentNumber++;
                    }
                }
                $sessionQuizzes = Dbase::GetQuizzes($sess['id']);
                $courseQuizzNumber += count($sessionQuizzes);
 
                foreach ($sessionQuizzes as $quz){
                    $answer = 0;
                    foreach($quz['choices'] as $key => $val){
                        if($key != "quiz"){
                            $answer+=$val;
                            $totalQuizAnswer+=$val;
                        }
                    }
                    if($answer>0){
                        $answerdCourseQuizzNumber++;
                    }
                }

            }
            $report['reports']['comments'] = array(
                'tottal_number_of_comments'       =>$courseCommentNumber ,
                'tottal_number_of_comments_by_students'=>$studentCourseCommentNumber ,
                'average_students_comments_per_session'      =>$studentCourseCommentNumber/count($courseSessions)
            );
            $report['reports']['questionnaires'] = array(
                'tottal_number_of_questionnaires'       =>$courseQuizzNumber ,
                'tottal_number_of_answered_questionnaires'=>$answerdCourseQuizzNumber ,
                'tottal_number_of_participants'      =>$totalQuizAnswer,
                'average_participation_per_questionnaire'      =>$totalQuizAnswer/$courseQuizzNumber,
                'average_answered_questionnaires_per_session'      =>$answerdCourseQuizzNumber/count($courseSessions)
            );
        }else{
            $reportError = true;
            $reportMessage = "You do not have access to this course!";
        }

    }else{
        $userCourses = Dbase::GetEnrollmentFromUser($userId);
        $courses = array();
        foreach($userCourses as $crs){
            if($crs['role_code'] == 'in'){
                $temp_course = Dbase::GetCourseInfo($crs['course_id']);
                $temp_course['title'] = $temp_course['title'] . " (" . Dbase::GetTermRef($temp_course['term_code']) . " " . $temp_course['year'] . ")";
                array_push($courses, $temp_course);
            }
        }
    }

    Dbase::Disconnect();
}