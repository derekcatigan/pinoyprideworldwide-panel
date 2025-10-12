<script setup>
import { computed, ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { usePhoneFormatter } from '@/composables/usePhoneFormatter.js'
import { useFlashToast, useFlashValidation } from '@/composables/useFlashToast.js'
import { capitalize } from '@/utils/stringHelper.js'
import { toast } from 'vue-sonner'

// Layouts & Components
import Layout from '@/layouts/Layout.vue'
import Card from '@/components/Card.vue'
import DataTable from '@/components/DataTable.vue'
import InfoAlert from '@/components/InfoAlert.vue'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()
const branches = computed(() => page.props.branches ?? [])
const locations = computed(() => page.props.locations ?? [])
const boxTypes = computed(() => page.props.boxTypes ?? [])
const existingBoxes = computed(() => page.props.existingBoxes ?? [])
const agents = computed(() => page.props.agents ?? [])

const form = useForm({
    senderName: '',
    senderContact: '',
    senderEmail: '',
    senderAddress: '',
    receiverName: '',
    receiverContact: '',
    receiverEmail: '',
    receiverAddress: '',
    invoiceNumber: '',
    datePickUp: '',
    boxType: '',
    length: '',
    height: '',
    width: '',
    price: '',
    location_id: '',
    branch_id: '',
    user_id: '',
    quantity: '',
    description: '',
    boxes: []
})

useFlashToast()

const senderContact = ref('PH')
const receiverContact = ref('PH')

usePhoneFormatter(
    {
        get: () => form.senderContact,
        set: (val) => form.senderContact = val
    },
    senderContact
)
usePhoneFormatter(
    {
        get: () => form.receiverContact,
        set: (val) => form.receiverContact = val
    },
    receiverContact
)

watch([() => form.boxType, () => form.branch_id, () => form.location_id],
    ([boxType, branchId, locationId]) => {
        if (!boxType || !branchId || !locationId) return

        const match = existingBoxes.value.find(b =>
            b.branch_id === branchId &&
            b.location_id === locationId &&
            b.box_type.box_type === boxType
        )

        if (match) {
            form.length = match.box_type.length
            form.height = match.box_type.height
            form.width = match.box_type.width
            form.price = match.box_type.price
        } else {
            form.length = ''
            form.height = ''
            form.width = ''
            form.price = ''
        }
    })

// Rate per Cubic Foot
const ratePerCubicFoot = 25

watch(
    () => [form.boxType, form.length, form.height, form.width],
    ([boxType, l, h, w]) => {
        if (boxType === 'odd' && l && h && w) {
            const cubicFeet = (l * h * w) / 1728
            form.price = (cubicFeet * ratePerCubicFoot).toFixed(2)
        }
    }
)

const filteredAgents = computed(() =>
    agents.value.filter(agent => agent.branch_id === form.branch_id)
)

function handleAddBox() {
    const { boxType, quantity, length, height, width, price, description } = form

    if (!boxType || !quantity || !length || !height || !width) {
        return toast.error('Missing box details or Quantity')
    }

    form.boxes.push({
        id: Date.now(),
        boxType,
        length,
        height,
        width,
        price,
        quantity,
        description,
        total: price * quantity,
    })

    Object.assign(form, {
        boxType: '',
        length: '',
        height: '',
        width: '',
        price: '',
        quantity: '',
        description: '',
    })
}

const removeBox = id => form.boxes = form.boxes.filter(b => b.id !== id)

function submit() {
    form.post('/order/store', {
        onSuccess: () => {
            form.reset()
        },
        onError: (error) => useFlashValidation(error)
    });
}

defineOptions({
    layout: Layout
})
</script>

<template>
    <ToastAlert />
    <Card>
        <template #title>
            <span>CREATE ORDER</span>
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <InfoAlert />
                <div class="flex flex-wrap items-center gap-3 p-3">
                    <div class="flex-1 min-w-[200px] border border-gray-300 p-3 rounded shadow">
                        <div class="divider divider-start">Sender Information</div>
                        <!-- Sender Name -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Sender Name</legend>
                            <input type="text" name="sender_name" v-model="form.senderName" class="w-full input"
                                placeholder="Enter sender name" />
                        </fieldset>

                        <!-- Sender Contact -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Sender Contact</legend>
                            <div class="flex gap-2">
                                <select v-model="senderContact" class="select w-32">
                                    <option value="PH">PH</option>
                                    <option value="US">US</option>
                                </select>
                                <input type="tel" name="sender_contact" v-model="form.senderContact"
                                    class="w-full input" placeholder="Enter sender contact" />
                            </div>
                        </fieldset>

                        <!-- Sender Email -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Sender Email</legend>
                            <input type="email" name="sender_email" v-model="form.senderEmail" class="w-full input"
                                placeholder="Enter sender email" />
                        </fieldset>

                        <!-- Sender Address -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Sender Address</legend>
                            <input type="text" name="sender_address" v-model="form.senderAddress" class="w-full input"
                                placeholder="Enter sender address" />
                        </fieldset>
                    </div>

                    <div class="flex-1 min-w-[200px] border border-gray-300 p-3 rounded shadow">
                        <div class="divider divider-start">Receiver Information</div>
                        <!-- Receiver Name -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Receiver Name</legend>
                            <input type="text" name="receiver_name" v-model="form.receiverName" class="w-full input"
                                placeholder="Enter receiver name" />
                        </fieldset>

                        <!-- Receiver Contact -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Receiver Contact</legend>
                            <div class="flex gap-2">
                                <select v-model="receiverContact" class="select w-32">
                                    <option value="PH">PH</option>
                                    <option value="US">US</option>
                                </select>
                                <input type="tel" v-model="form.receiverContact" name="receiver_contact"
                                    class="w-full input" placeholder="Enter receiver contact" />
                            </div>
                        </fieldset>

                        <!-- Receiver Email -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Receiver Email</legend>
                            <input type="email" v-model="form.receiverEmail" name="receiver_email" class="w-full input"
                                placeholder="Enter receiver email" />
                        </fieldset>

                        <!-- Receiver Address -->
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Receiver Address</legend>
                            <input type="text" v-model="form.receiverAddress" name="receiver_address"
                                class="w-full input" placeholder="Enter receiver address" />
                        </fieldset>
                    </div>
                </div>

                <div class="divider divider-start">Box Details</div>


                <div class="flex flex-wrap items-center gap-1">

                    <!-- Branch -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Branch</legend>
                        <select v-model="form.branch_id" class="w-full select">
                            <option disabled value="">Select branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                {{ branch.branch_name }}
                            </option>
                        </select>
                    </fieldset>

                    <!-- Destination -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Destination</legend>
                        <select v-model="form.location_id" class="w-full select">
                            <option disabled value="">Location</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id">
                                {{ location.location }}
                            </option>
                        </select>
                    </fieldset>

                    <!-- Agents Lists -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Assigned Agent</legend>
                        <select v-model="form.user_id" class="w-full select">
                            <option disabled value="">Assign Agent</option>
                            <option v-for="agent in filteredAgents" :key="agent.id" :value="agent.id">
                                {{ agent.user_code }} | {{ agent.profile.first_name }}
                            </option>
                        </select>
                    </fieldset>

                    <!-- Date Picked Up -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Date Picked Up</legend>
                        <input type="date" v-model="form.datePickUp" class="w-full input">
                    </fieldset>

                    <!-- Iinvoice -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Invoice Number</legend>
                        <input type="text" v-model="form.invoiceNumber" class="w-full input"
                            placeholder="Enter invoice number" />
                    </fieldset>

                </div>

                <div class="flex flex-wrap items-center gap-1 mt-3">

                    <!-- Box Type -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Box Type</legend>
                        <select v-model="form.boxType" class="w-full select">
                            <option disabled value="">Box type</option>
                            <option v-for="type in boxTypes" :key="type.value" :value="type.value">
                                {{ type.name }}
                            </option>
                        </select>
                    </fieldset>


                    <!-- Dimension -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Dimension</legend>
                        <div class="join join-vertical lg:join-horizontal">
                            <input type="number" v-model="form.length" class="w-full input join-item" placeholder="0"
                                :readonly="form.boxType !== 'odd'" />
                            <input type="number" v-model="form.height" class="w-full input join-item" placeholder="0"
                                :readonly="form.boxType !== 'odd'" />
                            <input type="number" v-model="form.width" class="w-full input join-item" placeholder="0"
                                :readonly="form.boxType !== 'odd'" />
                        </div>
                    </fieldset>

                    <!-- Price -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Price</legend>
                        <input type="number" v-model="form.price" class="w-full input" placeholder="0" readonly />
                    </fieldset>

                    <!-- Quantity -->
                    <fieldset class="fieldset flex-1 min-w-[200px]">
                        <legend class="fieldset-legend">Quantity</legend>
                        <input type="text" v-model="form.quantity" class="w-full input" placeholder="Enter quantity" />
                    </fieldset>

                </div>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Description</legend>
                    <textarea v-model="form.description" class="w-full textarea h-24"
                        placeholder="Enter description"></textarea>
                </fieldset>

                <button class="btn btn-block btn-warning my-5" @click.prevent="handleAddBox">
                    ADD BOX
                </button>

                <DataTable :headers="['Box Type', 'Quantity', 'Dimension', 'Fee', 'Description', 'Action']"
                    :rows="form.boxes">
                    <template #row="{ row }">
                        <td>{{ capitalize(row.boxType) }}</td>
                        <td>{{ row.quantity }}</td>
                        <td>{{ row.length }} x {{ row.width }} x {{ row.height }}</td>
                        <td>{{ row.price }}</td>
                        <td>{{ row.description }}</td>
                        <td>
                            <button class="btn btn-error btn-sm" @click="removeBox(row.id)">Remove</button>
                        </td>
                    </template>

                    <template #footer>
                        <tr>
                            <td colspan="3" class="text-right font-bold">Grand Total:</td>
                            <td colspan="3" class="font-bold">
                                {{form.boxes.reduce((sum, b) => sum + (b.price * b.quantity), 0)}}
                            </td>
                        </tr>
                    </template>
                </DataTable>
                <button type="submit" class="btn btn-block btn-primary mt-10" :disabled="form.processing">Add
                    Order</button>
            </form>
        </template>
    </Card>
</template>