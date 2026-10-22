import AbstractApiEntity from '@wexample/js-api-entity/Common/AbstractApiEntity';
import schema from '../data/entity/model.json';

export default class Model extends AbstractApiEntity {
  static readonly entityName = 'model';

  static retrieveEntitySchema() {
    return schema;
  }
}
