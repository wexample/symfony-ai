import AbstractApiRepository from '@wexample/js-api/Common/AbstractApiRepository';
import Agent from '../Entity/Agent.js';

export default class AgentRepository extends AbstractApiRepository<Agent> {
  static getEntityType() {
    return Agent;
  }
}
