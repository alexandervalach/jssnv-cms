<?php

declare(strict_types=1);

namespace App\Helpers;

use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class FormHelper
 * @package App
 */
class TestHelper {

  /**
   * @param $postData
   * @return bool|int|ActiveRow
   */
  public static function evaluateTest ($questions, $postData)
  {
    $levels = [];
    $levelsResults = [];
    $totalHighScore = 0;
    $totalScore = 0;

    // Value initialization
    foreach ($questions as $question) {
      if (empty($levels[$question->level_id])) {
        $levels[$question->level_id] = [
          'id' => $question->level_id,
          'score' => 0,
          'high_score' => 0
        ];
      }
    }

    // Get high score value (total and per level) and correct answers
  	foreach ($questions as $question) {
      $levels[$question->level_id]['high_score'] += $question->value;
      $answer[$question->id] = $question->related('answers')->where('correct', 1)->fetch();
      $totalHighScore += $question->value;
  	}

  	foreach ($questions as $question) {
  	  // Check if there is an answer for question
      if (!(array_key_exists('question' . $question->id, $postData))) {
        continue;
      }

      // Check if question correct
      if ((float) $postData['question' . $question->id] === (float) $answer[$question->id]->id) {
        $levels[$question->level_id]['score'] += $question->value;
        $totalScore += $question->value;
      }
    }

    return ArrayHash::from([
      'total_score' => round(($totalScore / (float) $totalHighScore) * 100, 2),
      'email' => array_key_exists('email', $postData) ? $postData['email'] : 'anonym',
      'levels' => $levels
    ]);
  }

  /**
   * @param Selection $questions
   * Transforms raw questions to simple array
   */
  public static function cookLevelsQuestions ($questions) 
  {
    $cookedLevels = self::cookLevels($questions);

    foreach ($questions as $question) {
      $cookedLevels[$question->level_id]['questions'][] = [ 
        'id' => $question->id,
        'label' => $question->label,
        'answers' => self::cookAnswers($question)
      ];
    }

    return ArrayHash::from($cookedLevels);
  }

  /** 
   * Transforms raw answers to simple array
   */
  protected static function cookAnswers ($question)
  {
    $answers = $question->related('answers');
    $cookedAnswers = [];
    
    foreach ($answers as $answer) {
      $cookedAnswers[] = [
        'id' => $answer->id,
        'label' => $answer->label
      ];
    }

    shuffle($cookedAnswers);

    return $cookedAnswers;
  }

  /**
   * @param Selection $questions
   * Extracts levels an assigns an array structure for template
   */
  protected static function cookLevels ($questions) 
  {
    if ($questions->count() == 0) {
      return [];
    }

    $prev = null;
    $cookedLevelsQuestions = [];

    foreach ($questions as $question) {
      if ($prev !== $question->level_id && $question->level_id != null) {
        $cookedLevelsQuestions[$question->level_id] = [ 
          'questions' => [ ],
          'label' => $question->ref('levels', 'level_id')->label
        ];
      }
      $prev = $question->level_id;
    }

    return $cookedLevelsQuestions;
  }

  /**
   * Render partial results values
   */
  public static function cookLevelsResults ($levels, $resultId) {
    $levelsResults = [];

    foreach ($levels as $level) {
      $levelsResults[] = [
        'result_id' => $resultId,
        'level_id' => $level['id'],
        'score' => round(($level['score'] / (float) $level['high_score']) * 100, 2)
      ];
    }

    return $levelsResults;
  }
}
