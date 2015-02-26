<?php

global $reportCourseId;
global $reportError;
global $reportMessage;
global $userId;
global $report;
global $courses;
global $asReport;
global $inReport;


if(!$reportError){
    $inReport = $asReport = $report = null;
    $assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor'];
    $admin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];
    Dbase::Connect();

    if($reportCourseId && $reportCourseId != ''){
        $roleInCourse = Dbase::GetUserRoleInCourse($userId, $reportCourseId);
        if( $roleInCourse == "in" || $assessor){
            $allStudents = Dbase::GetUsersFromCourse($reportCourseId, "st");
            $ids = array();
            foreach ($allStudents as $st) {
                if($st['studentid'] != 0){
                    array_push($ids, $st['studentid']);
                }
            }
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
            $report['reports']['students'] = array(
                'registered_students' => count($ids)>0 ? implode(", ", $ids) : "No Registered Ids"
            );
            $report['reports']['comments'] = array(
                'total_number_of_comments'       =>$courseCommentNumber ,
                'total_number_of_comments_by_students'=>$studentCourseCommentNumber ,
                'average_students_comments_per_session'      =>$studentCourseCommentNumber/count($courseSessions)
            );
            $report['reports']['questionnaires'] = array(
                'total_number_of_questionnaires'       =>$courseQuizzNumber ,
                'total_number_of_answered_questionnaires'=>$answerdCourseQuizzNumber ,
                'total_number_of_participants'      =>$totalQuizAnswer,
                'average_participation_per_questionnaire'      =>$totalQuizAnswer/$courseQuizzNumber,
                'average_answered_questionnaires_per_session'      =>$answerdCourseQuizzNumber/count($courseSessions)
            );
        }else {
            $reportError = true;
            $reportMessage = "You do not have access to this course!";
        }

    }else{
        
        if($assessor){
            $asReport['all-courses'] = Dbase::GetAssessorReport();
            $allCourses = Dbase::GetCourses();
            $courses = array();
            foreach($allCourses as $crs){
                $temp_course = Array();
                $temp_course['title'] = $crs['title'] . " (" . Dbase::GetTermRef($crs['term_code']) . " " . $crs['year'] . ")";
                $temp_course['id'] = $crs['id'];
                if($admin && Dbase::GetEnrollmentFromCourse($crs['id']) === null)
                    $temp_course['noInstructor'] = true;
                array_push($courses, $temp_course);
                
                $asReport['courses'][$crs['id']]['name'] = $temp_course['title'];
                $asReport['courses'][$crs['id']]['report'] = Dbase::GetAssessorReport($crs['id']);
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
            foreach($courses as $crs){
                    $inReport['courses'][$crs['id']]['name'] = $crs['title'];
                    $inReport['courses'][$crs['id']]['report'] = Dbase::GetAssessorReport($crs['id']);
            }
        }
        
    }

    Dbase::Disconnect();
}
