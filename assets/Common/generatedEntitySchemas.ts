import agent from '../data/entity/agent.json';
import claudeSession from '../data/entity/claude_session.json';
import message from '../data/entity/message.json';
import model from '../data/entity/model.json';
import session from '../data/entity/session.json';

type EntitySchema = { name: string };

export default function getGeneratedEntitySchemas(): Record<string, EntitySchema> {
  return {
    [agent.name]: agent,
    [claudeSession.name]: claudeSession,
    [message.name]: message,
    [model.name]: model,
    [session.name]: session,
  };
}
