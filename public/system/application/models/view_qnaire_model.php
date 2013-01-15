<?php

class View_qnaire_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getReviewer_apprv($empId, $token) {
        $data = array();
        $level = 0;
        $query = $this->db->get_where('employee', array('id' => $empId));
        foreach ($query->result() as $row) {
            $level = $row->level;
            break;
        }
        if ($token == 1) {
            array_push($data, "Please select a reviewer");
            $query2 = $this->db->query('SELECT id,lname, fname, mname FROM employee WHERE level>4 AND level<11 AND level>' . $level);
        } elseif ($token == 2) {
            array_push($data, "Please select an approving officer");
            $query2 = $this->db->query('SELECT id,lname, fname, mname FROM employee WHERE level>10 AND level>' . $level);
        }

        foreach ($query2->result() as $row2) {
            array_push($data, array(($row2->id) => ($row2->lname . " " . $row2->fname . " " . $row2->mname)));
        }
        return $data;
    }

    function getKPI() {
        $data = array();
        $query = $this->db->query('SELECT id, title FROM KPI');
        foreach ($query->result() as $row) {
            $data[$row->id] = $row->title;
        }
        return $data;
    }

    function getData($empId) {
        $data = array();
        $query = $this->db->query('SELECT self_score,reviewerScore,qnaire_id, id FROM emp_qnaire WHERE emp_id=' . $empId);
        foreach ($query->result() as $row) {
            $query2 = $this->db->query('SELECT eqh_reviewer_score,eqh_self_score,qnaire_head_id, id
                FROM emp_qnairehead WHERE eq_id=' . $row->id);
            foreach ($query2->result() as $row2) {
                $dataCount = count($data);
                if ($dataCount > 0) {
                    for ($i = 0; $i < $dataCount; $i++) {
                        if (isset($data[$i][$row2->qnaire_head_id])) {
                            $data[$i][$row2->qnaire_head_id]+=$row2->eqh_self_score;
                        } else {
                            array_push($data, array($row2->qnaire_head_id => $row2->eqh_self_score));
                        }
                    }
                } else {
                    array_push($data, array($row2->qnaire_head_id => $row2->eqh_self_score));
                }
            }
        }
        //print_r($data);
        return $data;
    }

    function getMaxScore($data) { ///input to the graph
        ///the input to this function is in this form array(0=>array(1==>2),1=>array(4=>5))
        $myData = array();
        for ($i = 0; $i < $count($data); $i++) {
            foreach ($data[$i] as $key => $value) {
                array_push($myData, $value);
            }
        }
        return $myData;
    }

    function getScores($headingId) {
        $data = array();
        $query = $this->db->get_where('headingscores', array('id' => $headingId));
        $i = 0;
        foreach ($query->result() as $row) {
            $data = array($row->EE, $row->ME, $row->NME, $row->MSE);

            break;
        }
        return $data;
    }

    function insert_emp_qnaire($data) {///note that this function does not insert into all the fields
        //of the emp_qnaire table
        $data1 = array(
            'qnaire_id' => $data['qnaire_id'],
            'project_id' => $data['project_id'],
            'emp_id' => $data['emp_id']);
        $this->db->where($data1);
        $query = $this->db->get('emp_qnaire');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data['id'] = $row->id;
                $this->db->update('emp_qnaire', $data);
                break;
            }
        } else {
            $this->db->insert('emp_qnaire', $data);
        }
    }

    function insertProject($name, $description) {
        $rowid = 0;
        $data = array(
            'name' => $name,
            'description' => $description);
        $this->db->where($data);
        $query = $this->db->get('project');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data['id'] = $row->id;
                $this->db->update('project', $data);
                $rowid = $row->id;
                break;
            }
        } else {
            $this->db->insert('project', $data);
            $this->db->where($data);
            $query = $this->db->get('project');
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $rowid = $row->id;
                    break;
                }
            }
        }
        return $rowid;
    }

    function calculateScores($answerString, $qnaireId) {
        $headScores = array();
        (float) $total = 0;
        $headings = $this->get_qnaire_headings($qnaireId);
        $j = 0;
        foreach ($headings as $heading) {
            $numOfquestions = count($this->get_qnaire_questions($heading->id));
            $scores = $this->getScores($heading->id);
            $scores[0] = ($scores[0] / $numOfquestions); //NME
            $scores[1] = ($scores[1] / $numOfquestions); //MSE
            $scores[2] = ($scores[2] / $numOfquestions); //ME
            $scores[3] = ($scores[3] / $numOfquestions); //EE
            for ($i = 0; $i < $numOfquestions; $i++) {
                $answer = (int) substr($answerString, $j, 1);
                $total = (float) ((float) $total + (float) $scores[($answer - 1)]);
                $j++;
            }
            array_push($headScores, $total);
            $total = 0;
        }
        return $headScores;
    }

    function get_all_data() {
        $data = array();

        return $data;
    }

    function get_empName($empId) {
        $name = "";
        $query = $this->db->query('SELECT lname, fname, mname FROM employee WHERE id=' . $empId);
        foreach ($query->result() as $row) {
            $name = $row->lname . " " . $row->fname . " " . $row->mname;
            break;
        }
        return $name;
    }

    function update_Qnaire($reviewerName, $approveName1, $approveName2, $emp_qnaireId, $answer, $qnaireId, $qnaireCmnt, $status, $head_comment_array, $token, $keyStrengths, $suggestns) {

        $headingScores = $this->calculateScores($answer, $qnaireId);
        $qnaireScore = array_sum($headingScores);
        $data1 = array(
            'qnaire_scomment' => $qnaireCmnt,
            'selfAnswerString' => $answer,
            'emp_qnaire_status' => 1,
            'self_score' => $qnaireScore,
            'reviewer' => $reviewerName, 'emp_mgr_id1' => $approveName1, 'emp_mgr_id2' => $approveName2);
        $data2 = array(
            'reviewerAnswerString' => $answer,
            'emp_qnaire_status' => 2,
            'keyStrengths' => $keyStrengths,
            'suggestions' => $suggestns,
            'reviewerScore' => $qnaireScore
        );
        $this->db->where('id', $emp_qnaireId);
        if ($token == 1)
            $this->db->update('emp_qnaire', $data1); //1 is for self assessment
 if ($token == 2

            )$this->db->update('emp_qnaire', $data2); //2 is for a reviewer update
 $query = $this->db->get_where('qnaire_head', array('qnaire_id' => $qnaireId));
        $i = 0;
        foreach ($query->result() as $row) {
            $this->insertHeadComments($emp_qnaireId, $head_comment_array[$i], $row->id, $headingScores[$i], $token);
            $i++;
        }
    }

    function insertHeadComments($eq_Id, $comment, $headId, $score, $token) {
        //echo $eq_Id . ":" . $headId . "<br>";
        $data1 = array(
            'eq_id' => $eq_Id,
            'eqh_scomment' => $comment,
            'qnaire_head_id' => $headId,
            'eqh_self_score' => $score);
        $data2 = array(
            'eq_id' => $eq_Id,
            'eqh_rcomment' => $comment,
            'qnaire_head_id' => $headId,
            'eqh_reviewer_score' => $score
        );
        if ($token == 1

            )$data = $data1;
        elseif ($token == 2

            )$data = $data2;
        //print_r($data);
        $this->db->where('eq_id', $eq_Id);
        $this->db->where('qnaire_head_id', $headId);
        $query = $this->db->get('emp_qnairehead');
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data['id'] = $row->id;
                $this->db->where('id', $row->id);
                $this->db->update('emp_qnairehead', $data);
                break;
            }
        } else {
            $this->db->insert('emp_qnairehead', $data);
        }
    }

    function getReview_qnaire($empId) {
        $temp = array();
        $query = $this->db->query('SELECT id,emp_id,emp_qnaire_status, emp_mgr_id1,emp_mgr_id2,due_date,project_id,reviewer,qnaire_id FROM emp_qnaire WHERE reviewer=' . $empId);
        foreach ($query->result() as $row) {
            $data = array();
            array_push($data, $row->id); //index 0
            $query2 = $this->db->query('SELECT title FROM qnaire WHERE id=' . $row->qnaire_id);
            foreach ($query2->result() as $row2) {
                array_push($data, $row2->title); //index 1
                break;
            }
            $query4 = $this->db->query('SELECT name FROM project WHERE id=' . $row->project_id);
            foreach ($query4->result() as $row4) {
                array_push($data, $row4->name); //index 2
                break;
            }
            $query3 = $this->db->query('SELECT lname, fname, mname FROM employee WHERE id=' . $row->emp_id);
            foreach ($query3->result() as $row3) {
                array_push($data, $row3->lname . " " . $row3->fname . " " . $row3->mname); //index 3
                break;
            }
            array_push($data, $row->due_date); //index 4
            array_push($data, $this->getStatus($row->emp_qnaire_status)); //index 5
            array_push($data, $row->emp_id); //index 6
            array_push($data, $row->qnaire_id); //index 7
            array_push($temp, $data);
        }
        return $temp;
    }

    function getApprove_qnaire($empId) {
        $temp = array();
        $query = $this->db->query('SELECT id,emp_id,emp_qnaire_status, emp_mgr_id1,emp_mgr_id2,due_date,project_id,reviewer,qnaire_id
            FROM emp_qnaire WHERE emp_mgr_id1=' . $empId . ' OR emp_mgr_id2=' . $empId);
        foreach ($query->result() as $row) {
            $data = array();
            array_push($data, $row->id); //index 0
            $query2 = $this->db->query('SELECT title FROM qnaire WHERE id=' . $row->qnaire_id);
            foreach ($query2->result() as $row2) {
                array_push($data, $row2->title); //index 1
                break;
            }
            $query4 = $this->db->query('SELECT name FROM project WHERE id=' . $row->project_id);
            foreach ($query4->result() as $row4) {
                array_push($data, $row4->name); //index 2
                break;
            }
            $query3 = $this->db->query('SELECT lname, fname, mname FROM employee WHERE id=' . $row->emp_id);
            foreach ($query3->result() as $row3) {
                array_push($data, $row3->lname . " " . $row3->fname . " " . $row3->mname); //index 3
                break;
            }
            array_push($data, $row->due_date); //index 4
            array_push($data, $this->getStatus($row->emp_qnaire_status)); //index 5
            array_push($data, $row->emp_id); //index 6
            array_push($temp, $data);
        }
        return $temp;
    }

    function getHeadScores($id) {
        $data = array();
        $query = $this->db->query('SELECT  eqh_reviewer_score,eqh_self_score FROM emp_qnairehead WHERE eq_id=' . $id);
        foreach ($query->result() as $row) {
            array_push($data, array($row->eqh_reviewer_score, $row->eqh_self_score));
        }
        return $data;
    }

    function get_emp_qnaire_by_id($id) {
        $data = array();
        $query = $this->db->query('SELECT  emp_qnaire_status,emp_id, emp_mgr_id1,emp_mgr_id2,due_date,project_id,
            reviewer,self_score,qnaire_scomment,selfAnswerString,reviewerAnswerString,reviewerScore,qnaire_id,
            keyStrengths,suggestions FROM emp_qnaire WHERE id=' . $id);
        foreach ($query->result() as $row) {
            $query2 = $this->db->query('SELECT title FROM qnaire WHERE id=' . $row->qnaire_id);
            foreach ($query2->result() as $row2) {
                array_push($data, $row2->title); //index 0
                break;
            }
            array_push($data, $row->qnaire_id); //index 1
            $query3 = $this->db->query('SELECT lname, fname, mname FROM employee WHERE id=' . $row->emp_id);
            foreach ($query3->result() as $row3) {
                array_push($data, $row3->lname . " " . $row3->fname . " " . $row3->mname); //index 2
                break;
            }
            $query4 = $this->db->query('SELECT name FROM project WHERE id=' . $row->project_id);
            foreach ($query4->result() as $row4) {
                array_push($data, $row4->name); //index 3
                break;
            }
            array_push($data, $row->due_date); //index 4
            array_push($data, $this->getStatus($row->emp_qnaire_status)); //index 5
            array_push($data, $row->reviewer); //index 6
            array_push($data, $row->emp_mgr_id1); //index 7
            array_push($data, $row->emp_mgr_id2); //index 8
            array_push($data, $row->self_score); //index 9
            array_push($data, $row->reviewerScore); //index 10
            array_push($data, $row->selfAnswerString); //index 11
            array_push($data, $row->reviewerAnswerString); //index 12
            array_push($data, $row->keyStrengths); //index 13
            array_push($data, $row->suggestions); //index 14
            array_push($data, $row->qnaire_scomment); //index 15
        }
        return $data;
    }

    function getStatus($id) {
        $status = "";
        if ($id == 0

            )$status = "Not filled";
        if ($id == 1

            )$status = "Filled";
        if ($id == 2

            )$status = "Reviewed";
        if ($id == 3

            )$status = "Approved 1";
        if ($id == 4

            )$status = "Approved 2";

        return $status;
    }

    function get_emp_qnaire($empId) {
        $temp = array();
        $query = $this->db->query('SELECT id, emp_qnaire_status, emp_mgr_id1,emp_mgr_id2,due_date,project_id,
            reviewer,self_score,reviewerScore,qnaire_id FROM emp_qnaire WHERE emp_id=' . $empId);
        foreach ($query->result() as $row) {
            $data = array();
            array_push($data, $row->id); //index 0
            $query2 = $this->db->query('SELECT title FROM qnaire WHERE id=' . $row->qnaire_id);
            foreach ($query2->result() as $row2) {
                array_push($data, $row2->title); //index 1
                break;
            }
            array_push($data, $row->qnaire_id); //index 2
            $query3 = $this->db->query('SELECT lname, fname, mname FROM employee WHERE id=' . $empId);
            foreach ($query3->result() as $row3) {
                array_push($data, $row3->lname . " " . $row3->fname . " " . $row3->mname); //index 3
                break;
            }
            $query4 = $this->db->query('SELECT name FROM project WHERE id=' . $row->project_id);
            foreach ($query4->result() as $row4) {
                array_push($data, $row4->name); //index 4
                break;
            }
            array_push($data, $row->due_date); //index 5
            array_push($data, $this->getStatus($row->emp_qnaire_status)); //index 6
            array_push($data, $row->reviewer); //index 7
            array_push($data, $row->emp_mgr_id1); //index 8
            array_push($data, $row->emp_mgr_id2); //index 9

            array_push($data, $row->self_score); //index 10
            array_push($data, $row->reviewerScore); //index 11
            array_push($temp, $data);
        }

        return $temp;
    }

    function getDeptId() {
        $temp = array();
        $query = $this->db->query('SELECT * FROM dept');
        foreach ($query->result() as $row) {
            array_push($temp, array($row->id => $row->name));
        }
        return $temp;
    }

    function getEmployee($deptId) {
        $temp = array();
        $query = $this->db->query('SELECT id, lname, fname, mname,sex FROM employee WHERE dept_id=' . $deptId);
        foreach ($query->result() as $row) {
            array_push($temp, array($row->id, $row->lname, $row->fname, $row->mname, $row->sex));
        }
        return $temp;
    }

    function get_qnaire_headings($qnaireId) {
        //echo $qnaireId. ":<br>";
        $qnaire_heads = array();
        $rows = 0;
        $query = $this->db->query('SELECT id, title, score FROM qnaire_head WHERE qnaire_id=' . $qnaireId);
        $rows = $query->num_rows();
        foreach ($query->result() as $row) {
            array_push($qnaire_heads, $row);
        }
        return $qnaire_heads;
    }

    function qnaire_title($id) {
        $query = $this->db->query('SELECT title FROM qnaire WHERE id=' . $id);
        $title = "";
        foreach ($query->result() as $row) {
            $title = $row->title;
        }
        return $title;
    }

    function get_qnaire_questions($headingId) {
        $questions = array();
        $rows = 0;
        $query = $this->db->query('SELECT question, questionType FROM question WHERE head_id=' . $headingId);
        $rows = $query->num_rows();
        foreach ($query->result() as $row) {
            array_push($questions, $row->question);
        }
        return $questions;
    }

    function get_numOfQuestion($qnaireId) {
        $questions = array();
        $rows = 0;
        $this->db->select('*');
        $this->db->from('qnaire_head');
        $this->db->join('question', 'question.head_id = qnaire_head.id');
        $this->db->where('qnaire_head.qnaire_id', $qnaireId);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            array_push($questions, $row);
        }
        return $questions;
    }

    function get_numOfHeads($qnaireId) {
        $headings = array();
        $rows = 0;
        $query = $this->db->query('SELECT * FROM qnaire_head WHERE qnaire_id=' . $qnaireId);
        $rows = $query->num_rows();
        foreach ($query->result() as $row) {
            array_push($headings, $row);
        }
        return $headings;
    }
    function getHeadTitle($id) {
    $title = "";
    $query = $this->db->query('SELECT qnaire_head_id FROM emp_qnairehead WHERE id=' . $id);
    foreach ($query->result() as $row) {
        $query1 = $this->db->query('SELECT title FROM qnaire_head WHERE id=' . $row->qnaire_head_id);
        foreach ($query1->result() as $row1) {
            $title = $row1->title;
            break;
        }
    }
    return $title;
}

}

?>
