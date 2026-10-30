<script>
import AbstractEntityChat from '@wexample/symfony-design-system/components/collection/chat/abstract-entity-chat/abstract-entity-chat.vue';
import ApiService from '@wexample/symfony-loader/js/Services/ApiService';
import Message from '../../../Entity/Message';

const ICON_BY_TYPE = {
  assistant: 'ph:bold/robot',
  error: 'ph:bold/warning-circle',
  system: 'ph:bold/gear',
  tool: 'ph:bold/wrench',
  user: 'ph:bold/user'
};

// What the server publishes on the session's topic when a turn is written.
const EVENT_MESSAGE_CREATED = 'message-created';

// A tool turn is one of two things, both json in its body: what the agent
// called — `name`, `arguments` —, or what the page answered — `tool`,
// `callId`, `results`. Only the first is the page's to run.
const TYPE_TOOL = 'tool';

export default {
  extends: AbstractEntityChat,

  template: '#vue-template-wexample-symfony-ai-bundle-vue-collection-chat-message-chat',

  props: {
    // A thread is the thread of one conversation, and which one is decided by
    // whoever places the component.
    sessionId: {
      type: String,
      required: true
    }
  },

  methods: {
    getEntityClass() {
      return Message;
    },

    getEntitiesFetchParams() {
      return {
        query: {
          session: this.sessionId
        }
      };
    },

    getPageLength() {
      return 20;
    },

    startsAtLastPage() {
      return true;
    },

    // What the operator writes is answered by an agent, in the background:
    // the thread shows the answer is coming.
    expectsReply() {
      return true;
    },

    // A message has no author of its own: who spoke is its type, and the label
    // is all the reader ever sees of it.
    getMessageAuthor(entity) {
      return this.trans(`@vue::author.${entity.type}`);
    },

    getMessageContent(entity) {
      return entity.body ?? '';
    },

    // The wait for an answer ends on what the agent says, not on what the
    // operator wrote.
    isOwnMessage(entity) {
      return entity.type === 'user';
    },

    readToolBody(entity) {
      if (entity.type !== TYPE_TOOL) {
        return null;
      }

      try {
        return JSON.parse(entity.body ?? '');
      } catch (error) {
        return null;
      }
    },

    // The call the agent made, null for any other line.
    readToolCall(entity) {
      const body = this.readToolBody(entity);

      return typeof body?.name === 'string' && !body.callId ? body : null;
    },

    isToolResult(entity) {
      return Boolean(this.readToolBody(entity)?.callId);
    },

    // The call's own turn names it: the engine answers a call by the id of
    // the turn that carried it.
    getToolCallId(entity) {
      return entity.id ?? null;
    },

    getConversationKey() {
      return this.sessionId;
    },

    async postToolResult(call, callId, results) {
      await this.app.getServiceOrFail(ApiService).getClient().post({
        path: 'message/create',
        options: {
          json: {
            session: this.sessionId,
            toolResult: {
              tool: call.name,
              callId,
              results
            }
          }
        }
      });
    },

    getMessageDate(entity) {
      return entity.dateCreated ?? null;
    },

    getMessageIcon(entity) {
      return ICON_BY_TYPE[entity.type] ?? ICON_BY_TYPE.user;
    },

    getMessageVariant(entity) {
      return entity.type;
    },

    // Who spoke is not sent: the endpoint knows a turn arriving from here was
    // spoken by the operator, and refuses anything else in the payload.
    buildMessageEntity(content) {
      return new Message({
        session: this.sessionId,
        body: content
      });
    },

    getLiveThread() {
      return {
        entityName: 'session',
        id: this.sessionId,
        event: EVENT_MESSAGE_CREATED
      };
    }
  }
};
</script>
