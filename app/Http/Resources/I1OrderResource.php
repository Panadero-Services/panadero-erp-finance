<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class I1OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tpt_order_nr' => $this->tpt_order_nr,
            'cust_order_nr' => $this->cust_order_nr,
            'i1_customer_id' => $this->i1_customer_id,
            'i1_supplier_id' => $this->i1_supplier_id,
            'r_date' => $this->r_date,
            'e_date' => $this->e_date,
            'a_date' => $this->a_date,
            'b_date' => $this->b_date,
            'i1_forwarder_id' => $this->i1_forwarder_id,
            'i1_source_site_id' => $this->i1_source_site_id,
            'i1_dest_site_id' => $this->i1_dest_site_id,
            'i1_product_id' => $this->i1_product_id,
            'order_qty' => $this->order_qty,
            'i1_storage_from_id' => $this->i1_storage_from_id,
            'i1_storage_to_id' => $this->i1_storage_to_id,
            'weight1' => $this->weight1,
            'weight2' => $this->weight2,
            'net_qty' => $this->net_qty,
            'density' => $this->density,
            'purity' => $this->purity,
            'docs_array' => $this->docs_array,
            'analyse_array' => $this->analyse_array,
            'i1_status_id' => $this->i1_status_id,
            'comment' => $this->comment,
            'is_active' => $this->is_active,
            'is_locked' => $this->is_locked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'customer' => $this->whenLoaded('customer'),
            'supplier' => $this->whenLoaded('supplier'),
            'forwarder' => $this->whenLoaded('forwarder'),
            'source_site' => $this->whenLoaded('sourceSite'),
            'dest_site' => $this->whenLoaded('destSite'),
            'product' => $this->whenLoaded('product'),
            'storage_from' => $this->whenLoaded('storageFrom'),
            'storage_to' => $this->whenLoaded('storageTo'),
            'status' => $this->whenLoaded('status'),
        ];
    }
} 