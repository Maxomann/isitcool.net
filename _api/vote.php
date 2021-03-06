<?php
include_once 'functions.php';
include_once 'vote_functions.php';
include_once 'spam_protection.php';
checkOrigin();

$action = $_REQUEST['action'];/*requestId, getVoteState, vote*/
if(isset($_REQUEST['id'])){
    $id = $_REQUEST['id'];
}
if(isset($_REQUEST['state'])){
    $voteState = $_REQUEST['state'];
}
if(getWordFromURL()!=""){
    $word = getWordFromURL();
}

$ip = getAnonymousIp();

if( $action == 'requestId' ){
    $returnId = generateId();

    $result = array(
        'status' => 'success',
        'id' => (string)$returnId
    );
}else if( $action == 'getVoteState' ){
    if(isset($id) && isset($word)){
        $voteState = getVoteState($id, $word);
        if($voteState!=false){
            $result = array(
                'status' => 'success',
                'id' => $id,
                'word' => $word,
                'voteState' => $voteState
            );
        }
    }
}else if( $action == 'vote' ){
    if( spamcheck($ip) ){
        if(isset($id) && isset($word) && isset($voteState)){
            $voteResult = vote($id, $word, $voteState);
            if($voteResult!=false){
                $coolness = getCoolnessForWord($word);
                $result = array(
                    'status' => 'success',
                    'id' => $id,
                    'word' => $word,
                    'voteState' => $voteResult,
                    'coolness' => $coolness
                );
            }
        }
    }
}

if(!isset($result)){
    $result = array(
        'status' => 'error'
    );
}
echo json_encode($result);

?>
