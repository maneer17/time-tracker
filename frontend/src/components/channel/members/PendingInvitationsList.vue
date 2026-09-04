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
        class="border-2 border-transparent hover:border-[#FFE5E0] transition-colors rounded-[1.8rem]"
      />
    </div>

    <div v-else class="flex flex-col items-center justify-center py-10 px-6 bg-[#F9F7F2]/50 rounded-[2.5rem] border-2 border-dashed border-[#F2E9E4]">
      <div class="bg-white p-3 rounded-full shadow-sm mb-3 text-xl">✉️</div>
      <p class="text-sm text-[#D4A373] font-black uppercase tracking-[0.15em] italic">
        No pending invitations.
      </p>
    </div>
  </div>
</template>