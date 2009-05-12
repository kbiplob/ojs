<?php

/**
 * @file SubmissionCommentsHandler.inc.php
 *
 * Copyright (c) 2003-2009 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class SubmissionCommentsHandler
 * @ingroup pages_layoutEditor
 *
 * @brief Handle requests for submission comments. 
 */

// $Id$


import('pages.layoutEditor.SubmissionLayoutHandler');

class SubmissionCommentsHandler extends LayoutEditorHandler {
	/**
	 * Constructor
	 **/
	function SubmissionCommentsHandler() {
		parent::LayoutEditorHandler();
	}
	/** comment associated with the request **/
	var $comment;
	
	/**
	 * View layout comments.
	 */
	function viewLayoutComments($args) {
		$this->validate();
		$this->setupTemplate(true);

		$articleId = $args[0];

		$submissionLayoutHandler =& new SubmissionLayoutHandler();
		$submissionLayoutHandler->validate($articleId);
		$submission =& $submissionLayoutHandler->submission;
		LayoutEditorAction::viewLayoutComments($submission);

	}

	/**
	 * Post layout comment.
	 */
	function postLayoutComment() {
		$this->validate();
		$this->setupTemplate(true);

		$articleId = Request::getUserVar('articleId');

		// If the user pressed the "Save and email" button, then email the comment.
		$emailComment = Request::getUserVar('saveAndEmail') != null ? true : false;

		$submissionLayoutHandler =& new SubmissionLayoutHandler();
		$submissionLayoutHandler->validate($articleId);
		$submission =& $submissionLayoutHandler->submission;
		if (LayoutEditorAction::postLayoutComment($submission, $emailComment)) {
			LayoutEditorAction::viewLayoutComments($submission);
		}

	}

	/**
	 * View proofread comments.
	 */
	function viewProofreadComments($args) {
		$this->validate();
		$this->setupTemplate(true);

		$articleId = $args[0];

		$submissionLayoutHandler =& new SubmissionLayoutHandler();
		$submissionLayoutHandler->validate($articleId);
		$submission =& $submissionLayoutHandler->submission;
		LayoutEditorAction::viewProofreadComments($submission);

	}

	/**
	 * Post proofread comment.
	 */
	function postProofreadComment() {
		$this->validate();
		$this->setupTemplate(true);

		$articleId = Request::getUserVar('articleId');

		// If the user pressed the "Save and email" button, then email the comment.
		$emailComment = Request::getUserVar('saveAndEmail') != null ? true : false;

		$submissionLayoutHandler =& new SubmissionLayoutHandler();
		$submissionLayoutHandler->validate($articleId);
		$submission =& $submissionLayoutHandler->submission;
		if (LayoutEditorAction::postProofreadComment($submission, $emailComment)) {
			LayoutEditorAction::viewProofreadComments($submission);
		}

	}

	/**
	 * Edit comment.
	 */
	function editComment($args) {
		$this->validate();
		$this->setupTemplate(true);

		$articleId = $args[0];
		$commentId = $args[1];

		$submissionLayoutHandler =& new SubmissionLayoutHandler();
		$submissionLayoutHandler->validate($articleId);
		$submission =& $submissionLayoutHandler->submission;
	
		$this->validate($commentId);
		$comment =& $this->comment;
		LayoutEditorAction::editComment($submission, $comment);

	}

	/**
	 * Save comment.
	 */
	function saveComment() {
		$this->validate();
		$this->setupTemplate(true);

		$articleId = Request::getUserVar('articleId');
		$commentId = Request::getUserVar('commentId');

		// If the user pressed the "Save and email" button, then email the comment.
		$emailComment = Request::getUserVar('saveAndEmail') != null ? true : false;

		$submissionLayoutHandler =& new SubmissionLayoutHandler();
		$submissionLayoutHandler->validate($articleId);
		$submission =& $submissionLayoutHandler->submission;
	
		$this->validate($commentId);
		$comment =& $this->comment;
		LayoutEditorAction::saveComment($submission, $comment, $emailComment);

		// Redirect back to initial comments page
		if ($comment->getCommentType() == COMMENT_TYPE_LAYOUT) {
			Request::redirect(null, null, 'viewLayoutComments', $articleId);
		} else if ($comment->getCommentType() == COMMENT_TYPE_PROOFREAD) {
			Request::redirect(null, null, 'viewProofreadComments', $articleId);
		}
	}

	/**
	 * Delete comment.
	 */
	function deleteComment($args) {
		$this->validate();
		$this->setupTemplate(true);

		$articleId = $args[0];
		$commentId = $args[1];

		$submissionLayoutHandler =& new SubmissionLayoutHandler();
		$submissionLayoutHandler->validate($articleId);
		$submission =& $submissionLayoutHandler->submission;
	
		$this->validate($commentId);
		$comment =& $this->comment;
		LayoutEditorAction::deleteComment($commentId);

		// Redirect back to initial comments page
		if ($comment->getCommentType() == COMMENT_TYPE_LAYOUT) {
			Request::redirect(null, null, 'viewLayoutComments', $articleId);
		} else if ($comment->getCommentType() == COMMENT_TYPE_PROOFREAD) {
			Request::redirect(null, null, 'viewProofreadComments', $articleId);
		}
	}

	//
	// Validation
	//

	/**
	 * Validate that the user is the author of the comment.
	 */
	function validate($commentId) {
		parent::validate();

		$isValid = true;
 
		$articleCommentDao =& DAORegistry::getDAO('ArticleCommentDAO');
		$user =& Request::getUser();

		$comment =& $articleCommentDao->getArticleCommentById($commentId);

		if ($comment == null) {
			$isValid = false;

		} else if ($comment->getAuthorId() != $user->getUserId()) {
			$isValid = false;
		}

		if (!$isValid) {
			Request::redirect(null, Request::getRequestedPage());
		}
		
		$this->comment =& $comment;
		return true;
	}
}
?>
