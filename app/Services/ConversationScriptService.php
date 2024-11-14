<?php

namespace App\Services;

class ConversationScriptService
{
    public function findAction(int $id, array $conversationNodes): ?object
    {
        foreach ($conversationNodes as $node) {
            if ($node->id === $id) {
                return $node;
            }

            if (count($node->children) > 0) {
                $result = $this->findAction($id, $node->children);

                if ($result) {
                    return $result;
                }
            }
        }
    }
}
