import AbstractApiEntity from '@wexample/js-api/Common/AbstractApiEntity';
import schema from '../data/entity/agent.json';

export default class Agent extends AbstractApiEntity {
  static readonly entityName = 'agent';

  static retrieveEntitySchema() {
    return schema;
  }
}
