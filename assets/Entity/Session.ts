import AbstractApiEntity from '@wexample/js-api-entity/Common/AbstractApiEntity';
import type { ApiEntitySchema } from '@wexample/js-api-entity/Common/AbstractApiEntity';
import schema from '../data/entity/session.json';

export default class Session extends AbstractApiEntity {
  static readonly entityName = 'session';

  static retrieveEntitySchema(): ApiEntitySchema {
    return schema;
  }
}
