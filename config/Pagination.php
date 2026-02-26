<?php
class Pagination {
    /**
     * Construir query con paginación, ordenamiento y búsqueda
     * 
     * @param string $baseQuery Query base sin WHERE, ORDER BY, LIMIT
     * @param array $params Parámetros de paginación
     * @param array $searchFields Campos donde buscar
     * @return array ['query' => string, 'countQuery' => string, 'params' => array]
     */
    public static function buildQuery($baseQuery, $params = [], $searchFields = []) {
        $page = isset($params['page']) ? max(1, intval($params['page'])) : 1;
        $limit = isset($params['limit']) ? max(1, min(100, intval($params['limit']))) : 10;
        $sort = isset($params['sort']) ? $params['sort'] : 'id';
        $order = isset($params['order']) && strtoupper($params['order']) === 'ASC' ? 'ASC' : 'DESC';
        $search = isset($params['search']) ? trim($params['search']) : '';
        
        $offset = ($page - 1) * $limit;
        
        // Query para contar total
        $countQuery = "SELECT COUNT(*) as total FROM ($baseQuery) as count_table";
        
        // Agregar búsqueda si existe
        $whereConditions = [];
        $bindParams = [];
        
        if (!empty($search) && !empty($searchFields)) {
            $searchConditions = [];
            foreach ($searchFields as $field) {
                $searchConditions[] = "$field LIKE :search";
            }
            $whereConditions[] = '(' . implode(' OR ', $searchConditions) . ')';
            $bindParams[':search'] = "%$search%";
        }
        
        // Construir query final
        $finalQuery = $baseQuery;
        
        if (!empty($whereConditions)) {
            $finalQuery .= ' WHERE ' . implode(' AND ', $whereConditions);
            $countQuery = "SELECT COUNT(*) as total FROM ($finalQuery) as count_table";
        }
        
        $finalQuery .= " ORDER BY $sort $order LIMIT :limit OFFSET :offset";
        
        return [
            'query' => $finalQuery,
            'countQuery' => $countQuery,
            'bindParams' => $bindParams,
            'limit' => $limit,
            'offset' => $offset,
            'page' => $page
        ];
    }
    
    /**
     * Construir respuesta paginada estándar
     * 
     * @param array $data Datos de la página actual
     * @param int $total Total de registros
     * @param int $page Página actual
     * @param int $limit Registros por página
     * @return array Respuesta formateada
     */
    public static function buildResponse($data, $total, $page, $limit) {
        $totalPages = ceil($total / $limit);
        
        return [
            'data' => $data,
            'pagination' => [
                'total' => intval($total),
                'page' => intval($page),
                'limit' => intval($limit),
                'totalPages' => intval($totalPages),
                'hasNext' => $page < $totalPages,
                'hasPrev' => $page > 1
            ]
        ];
    }
}
