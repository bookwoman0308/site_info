<?php

/**
 * @file
 * Contains \Drupal\site_info\Controller\TestController.
 */

namespace Drupal\site_info\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Access\AccessResult;

/**
 * Controller routines for site_info routes.
 */
class TestController extends ControllerBase {

  /**
   * This function provides custom access for the route at /{key}/{nid}.
   *
   * @param $key and $nid
   *   Passed by argument to the controller and NULL by default.
   *
   * @return AccessResult
   *   Return allowed only if the nid and API site key are proven to exist. Otherwise, return access denied.
   */
  public function access($key = NULL, $nid = NULL) {
    $config = \Drupal::service('config.factory')->getEditable('site_info.settings');
    $config_key = $config->get('apisitekey');
    $values = \Drupal::entityQuery('node')->condition('nid', $nid)->execute();
    if ($key == $config_key && !empty($values)) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

  /**
   * This function provides the output of a database query in JSON format for the nid passed as a parameter.
   *
   * @param $key and $nid
   *    Passed by argument to the controller and NULL by default.
   *
   * @return JsonResponse 
   *    Contains the database result in JSON format so long as the database query succeeds.
   */
  public function retrieve($key = NULL, $nid = NULL) {
    try {
      $query = db_query("SELECT * FROM {node} WHERE nid = :nid", [':nid' => $nid]);
      $result = $query->fetchAssoc();
      if (!empty($result)) {
        $response['data'] = $result;
      }
     }
    catch (\Exception $error) {
      return new JsonResponse($error->getMessage(), 400);
    }
    return new JsonResponse($response, 200);
  }

}
