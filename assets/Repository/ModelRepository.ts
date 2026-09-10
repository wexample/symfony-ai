import AbstractApiRepository from '@wexample/js-api/Common/AbstractApiRepository';
import Model from '../Entity/Model.js';

export default class ModelRepository extends AbstractApiRepository<Model> {
  static getEntityType() {
    return Model;
  }
}
