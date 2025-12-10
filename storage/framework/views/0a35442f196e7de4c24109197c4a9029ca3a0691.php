<?php $__env->startSection('title', __('messages.edit_appointment')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    <?php echo e(__('messages.edit_appointment')); ?>

                </h2>
                <a href="<?php echo e(route('showers.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    <?php echo e(__('messages.back')); ?>

                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('showers.update', $shower->id)); ?>" method="POST" id="showerForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <!-- User Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">
                                    <?php echo e(__('messages.user')); ?> <span class="text-danger">*</span>
                                </label>
                                <select name="user_id" id="user_id" class="form-control <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value=""><?php echo e(__('messages.select_user')); ?></option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" 
                                                <?php echo e((old('user_id', $shower->user_id) == $user->id) ? 'selected' : ''); ?>>
                                            <?php echo e($user->name); ?> - <?php echo e($user->email); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Patient Code -->
                            <div class="col-md-6 mb-3">
                                <label for="code_patient" class="form-label"><?php echo e(__('messages.patient_code')); ?></label>
                                <select name="code_patient" id="code_patient" class="form-control <?php $__errorArgs = ['code_patient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value=""><?php echo e(__('messages.select_room')); ?></option>
                                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($room->code); ?>" 
                                                data-discount="<?php echo e($room->discount); ?>"
                                                <?php echo e((old('code_patient', $shower->code_patient) == $room->code) ? 'selected' : ''); ?>>
                                            <?php echo e($room->title); ?> - <?php echo e($room->code); ?>

                                            <?php if($room->discount > 0): ?>
                                                (<?php echo e(__('messages.discount')); ?> <?php echo e($room->discount); ?>%)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['code_patient'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted"><?php echo e(__('messages.if_patient_in_room')); ?></small>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6 mb-3">
                                <label for="date_of_shower" class="form-label">
                                    <?php echo e(__('messages.date_of_shower')); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="date_of_shower" 
                                       id="date_of_shower" 
                                       class="form-control <?php $__errorArgs = ['date_of_shower'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('date_of_shower', $shower->date_of_shower->format('Y-m-d'))); ?>"
                                       required>
                                <?php $__errorArgs = ['date_of_shower'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Time -->
                            <div class="col-md-6 mb-3">
                                <label for="time_of_shower" class="form-label"><?php echo e(__('messages.time_of_shower')); ?></label>
                                <input type="time" 
                                       name="time_of_shower" 
                                       id="time_of_shower" 
                                       class="form-control <?php $__errorArgs = ['time_of_shower'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       value="<?php echo e(old('time_of_shower', $shower->time_of_shower ? $shower->time_of_shower->format('H:i') : '')); ?>">
                                <?php $__errorArgs = ['time_of_shower'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <?php echo e(__('messages.payment_method')); ?> <span class="text-danger">*</span>
                                </label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="payment_type" id="payment_cash" value="cash" 
                                           <?php echo e(!$shower->card_number_id ? 'checked' : ''); ?>>
                                    <label class="btn btn-outline-success" for="payment_cash">
                                        <i class="fas fa-money-bill-wave me-2"></i><?php echo e(__('messages.cash')); ?>

                                    </label>

                                    <input type="radio" class="btn-check" name="payment_type" id="payment_card" value="card"
                                           <?php echo e($shower->card_number_id ? 'checked' : ''); ?>>
                                    <label class="btn btn-outline-primary" for="payment_card">
                                        <i class="fas fa-credit-card me-2"></i><?php echo e(__('messages.card')); ?>

                                    </label>
                                </div>
                            </div>

                            <!-- Price (for cash payment) -->
                            <div class="col-md-6 mb-3" id="price_field" style="<?php echo e($shower->card_number_id ? 'display: none;' : ''); ?>">
                                <label for="price" class="form-label">
                                    <?php echo e(__('messages.price')); ?> <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="price" 
                                           id="price" 
                                           class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           value="<?php echo e(old('price', $shower->price)); ?>"
                                           step="0.01"
                                           min="0"
                                           <?php echo e(!$shower->card_number_id ? 'required' : ''); ?>>
                                    <span class="input-group-text"><?php echo e(__('messages.currency')); ?></span>
                                </div>
                                <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <small class="text-muted"><?php echo e(__('messages.default_price')); ?>: <?php echo e($defaultPrice); ?></small>
                            </div>

                            <!-- Card Selection (for card payment) -->
                            <div class="col-md-6 mb-3" id="card_field" style="<?php echo e(!$shower->card_number_id ? 'display: none;' : ''); ?>">
                                <label for="card_number_id" class="form-label"><?php echo e(__('messages.select_card')); ?></label>
                                <select name="card_number_id" id="card_number_id" class="form-control <?php $__errorArgs = ['card_number_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value=""><?php echo e(__('messages.select_card')); ?></option>
                                    <?php $__currentLoopData = $availableCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($card->id); ?>" 
                                                data-price="<?php echo e($card->card->price); ?>"
                                                data-name="<?php echo e($card->card->name); ?>"
                                                <?php echo e((old('card_number_id', $shower->card_number_id) == $card->id) ? 'selected' : ''); ?>>
                                            <?php echo e($card->number); ?> - <?php echo e($card->card->name); ?> (<?php echo e(number_format($card->card->price, 2)); ?> <?php echo e(__('messages.currency')); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['card_number_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div id="card_info" class="alert alert-warning mt-2" style="display: none;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="card_info_text"></span>
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="col-md-12 mb-3">
                                <label for="note" class="form-label"><?php echo e(__('messages.notes')); ?></label>
                                <textarea name="note" 
                                          id="note" 
                                          rows="3" 
                                          class="form-control <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          placeholder="<?php echo e(__('messages.add_notes')); ?>"><?php echo e(old('note', $shower->note)); ?></textarea>
                                <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-md-12">
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i><?php echo e(__('messages.update_appointment')); ?>

                                </button>
                                <a href="<?php echo e(route('showers.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i><?php echo e(__('messages.cancel')); ?>

                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentCash = document.getElementById('payment_cash');
    const paymentCard = document.getElementById('payment_card');
    const priceField = document.getElementById('price_field');
    const cardField = document.getElementById('card_field');
    const priceInput = document.getElementById('price');
    const cardSelect = document.getElementById('card_number_id');
    const cardInfo = document.getElementById('card_info');
    const cardInfoText = document.getElementById('card_info_text');
    const defaultPrice = <?php echo e($defaultPrice); ?>;
    const currency = '<?php echo e(__("messages.currency")); ?>';

    // Toggle payment method fields
    function togglePaymentFields() {
        if (paymentCard.checked) {
            priceField.style.display = 'none';
            cardField.style.display = 'block';
            priceInput.removeAttribute('required');
        } else {
            priceField.style.display = 'block';
            cardField.style.display = 'none';
            priceInput.setAttribute('required', 'required');
            cardSelect.value = '';
            cardInfo.style.display = 'none';
        }
    }

    paymentCash.addEventListener('change', togglePaymentFields);
    paymentCard.addEventListener('change', togglePaymentFields);

    // Handle card selection
    cardSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const cardPrice = selectedOption.dataset.price;
            const cardName = selectedOption.dataset.name;
            
            cardInfo.style.display = 'block';
            cardInfoText.innerHTML = `<?php echo e(__('messages.card_price')); ?>: <strong>${parseFloat(cardPrice).toFixed(2)} ${currency}</strong> - ${cardName}`;
            
            priceInput.value = cardPrice;
        } else {
            cardInfo.style.display = 'none';
            priceInput.value = '';
        }
    });

    // Initialize on page load
    togglePaymentFields();
    
    // Show card info if card is already selected
    if (cardSelect.value) {
        cardSelect.dispatchEvent(new Event('change'));
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/showers/edit.blade.php ENDPATH**/ ?>