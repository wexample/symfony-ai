<script>
import AbstractEntityChat from '@wexample/symfony-design-system/components/collection/chat/abstract-entity-chat/abstract-entity-chat.vue';
import AbstractEntityCollectionVueMixin from '@wexample/symfony-design-system/js/Vue/AbstractEntityCollectionVueMixin';
import AgentToolsService from '@wexample/symfony-loader/js/Services/AgentToolsService';
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

    // A message has no author of its own: who spoke is its type, and the label
    // is all the reader ever sees of it.
    getMessageAuthor(entity) {
      return this.trans(`@vue::author.${entity.type}`);
    },

    getMessageContent(entity) {
      return entity.body ?? '';
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
    },

    // A tool call arrives on the session's topic like any turn: the page runs
    // the tool its components declare (AgentToolsService), then tells the
    // engine what came of it, and the engine answers from there.
    onLiveSourceMessage(payload, source) {
      AbstractEntityCollectionVueMixin.methods.onLiveSourceMessage.call(this, payload, source);

      const message = payload?.data?.entity ?? payload?.data;
      if (payload?.event === EVENT_MESSAGE_CREATED && message?.type === TYPE_TOOL) {
        this.runToolCall(message);
      }
    },

    async runToolCall(message) {
      let call;
      try {
        call = JSON.parse(message.body ?? '');
      } catch (error) {
        return;
      }

      if (!message.id || typeof call?.name !== 'string' || call.callId) {
        return;
      }

      // Run once per page, however many times the call reaches it: null when
      // it already ran, and the engine already has its answer.
      const outcome = await this.app.getServiceOrFail(AgentToolsService).run(message.id, call.name, call.arguments ?? {});
      if (outcome === null) {
        return;
      }

      this.awaitingReply = true;
      await this.app.getServiceOrFail(ApiService).getClient().post({
        path: 'message/create',
        options: {
          json: {
            session: this.sessionId,
            toolResult: {
              tool: call.name,
              callId: message.id,
              results: [outcome.error !== undefined ? { error: outcome.error } : outcome.value]
            }
          }
        }
      });
    }
  }
};
</script>
