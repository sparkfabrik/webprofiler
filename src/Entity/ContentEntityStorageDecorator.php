<?php

namespace Drupal\webprofiler\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\Sql\SqlEntityStorageInterface;

class ContentEntityStorageDecorator extends EntityDecorator implements ContentEntityStorageInterface, SqlEntityStorageInterface {

  final public function __construct(ContentEntityStorageInterface $content_entity_storage) {
    parent::__construct($content_entity_storage);

    $this->entities = [];
  }

  public function createWithSampleValues($bundle = FALSE, array $values = []) {
    return $this->getOriginalObject()->createWithSampleValues($bundle, $values);
  }

  public function resetCache(array $ids = NULL) {
    return $this->getOriginalObject()->resetCache($ids);
  }

  public function loadMultiple(array $ids = NULL) {
    return $this->getOriginalObject()->loadMultiple($ids);
  }

  public function load($id) {
    return $this->getOriginalObject()->load($id);
  }

  public function loadUnchanged($id) {
    return $this->getOriginalObject()->loadUnchanged($id);
  }

  public function loadRevision($revision_id) {
    return $this->getOriginalObject()->loadRevision($revision_id);
  }

  public function deleteRevision($revision_id) {
    return $this->getOriginalObject()->deleteRevision($revision_id);
  }

  public function loadByProperties(array $values = []) {
    return $this->getOriginalObject()->loadByProperties($values);
  }

  public function create(array $values = []) {
    return $this->getOriginalObject()->create($values);
  }

  public function delete(array $entities) {
    return $this->getOriginalObject()->delete($entities);
  }

  public function save(EntityInterface $entity) {
    return $this->getOriginalObject()->save($entity);
  }

  public function restore(EntityInterface $entity) {
    return $this->getOriginalObject()->restore($entity);
  }

  public function hasData() {
    return $this->getOriginalObject()->hasData();
  }

  public function getQuery($conjunction = 'AND') {
    return $this->getOriginalObject()->getQuery($conjunction);
  }

  public function getAggregateQuery($conjunction = 'AND') {
    return $this->getOriginalObject()->getAggregateQuery($conjunction);
  }

  public function getEntityTypeId() {
    return $this->getOriginalObject()->getEntityTypeId();
  }

  public function getEntityType() {
    return $this->getOriginalObject()->getEntityType();
  }

  public function getEntityClass(?string $bundle = NULL): string {
    return $this->getOriginalObject()->getEntityClass($bundle);
  }

  public function loadMultipleRevisions(array $revision_ids) {
    return $this->getOriginalObject()->loadMultipleRevisions($revision_ids);
  }

  public function getLatestRevisionId($entity_id) {
    return $this->getOriginalObject()->getLatestRevisionId($entity_id);
  }

  public function createRevision(RevisionableInterface $entity, $default = TRUE, $keep_untranslatable_fields = NULL) {
    return $this->getOriginalObject()->createRevision($entity, $default, $keep_untranslatable_fields);
  }

  public function getLatestTranslationAffectedRevisionId($entity_id, $langcode) {
    return $this->getOriginalObject()->getLatestTranslationAffectedRevisionId($entity_id, $langcode);
  }

  public function createTranslation(ContentEntityInterface $entity, $langcode, array $values = []) {
    return $this->getOriginalObject()->createTranslation($entity, $langcode, $values);
  }

  public function getTableMapping(array $storage_definitions = NULL) {
    return $this->getOriginalObject()->getTableMapping($storage_definitions);
  }

}
