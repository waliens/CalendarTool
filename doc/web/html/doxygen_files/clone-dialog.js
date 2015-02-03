define([
  'aui',
  'jquery',
  'on-ready',
  'underscore',
  'utils/dialog-positions',
  'components/clone/dialog-function-helper',
  'components/clone/close-dialog-helper'
], function (
  AJS,
  $,
  onReady,
  _,
  dialogPositions,
  dialogFunctionHelper,
  closeDialogHelper
) {

  var dialogOptions = {
    width: 360,
    preHideCallback: closeDialogHelper.callback,
    gravity: 'w',
    offsetY: dialogPositions.sidebarOffsetY,
    arrowOffsetY: dialogPositions.sidebarArrowOffsetY,
    calculatePositions: dialogPositions.replaceExisting,
    // Use live events to make sure it works in the sidebar actions flyout
    useLiveEvents: true
  };

  return onReady(function ($trigger) {
    var
      id = $trigger.attr('id'),
      createDialog = dialogFunctionHelper('#repo-clone-dialog', 'repo-clone-dialog', dialogOptions);

    if (!$trigger.selector && id) {
      // onReady returns a jQuery element without a selector property, but
      // useLiveEvents requires that the selector property exist
      $trigger = $('#' + id)
    }

    createDialog($trigger);
  });

});
