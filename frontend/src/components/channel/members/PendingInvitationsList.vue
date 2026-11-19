<script setup>
import InvitationCard from '../../invitations/InvitationCard.vue'

const props = defineProps(['invitations'])
const emit = defineEmits(['cancelled'])
</script>

<template>
  <div class="space-y-4">
    <div v-if="props.invitations?.length > 0" class="flex flex-col gap-3">
      <InvitationCard
        v-for="invitation in props.invitations"
        :key="invitation.id"
        :invitation="invitation"
        :mode="'sent'"
        @cancelled="emit('cancelled', $event)"
      />
    </div>

    <div v-else class="py-10 px-6 bg-white rounded-3xl border border-dashed border-[#D9D4CE] text-center">
      <p class="text-sm text-[#5F7480]">{{ $t('channel_members.no_pending') }}</p>
    </div>
  </div>
</template>
