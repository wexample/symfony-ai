<script>
import AbstractEntityChat from '@wexample/symfony-design-system/vue/collection/chat/abstract-entity-chat.vue';
import Message from '../../../Entity/Message';

const ICON_BY_TYPE = {
  assistant: 'ph:bold/robot',
  system: 'ph:bold/gear',
  user: 'ph:bold/user'
};

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

    buildMessageEntity(content) {
      return new Message({
        session: this.sessionId,
        type: 'user',
        body: content
      });
    }
  }
};
</script>
